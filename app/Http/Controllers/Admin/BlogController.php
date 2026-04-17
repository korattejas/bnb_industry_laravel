<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;


class BlogController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message', 'Something went wrong!');
        $this->exception_error_code = config('custom.exception_error_code', 500);
        $this->validator_error_code = config('custom.validator_error_code', 422);
        $this->controller_name = "Admin/BlogController";
    }

    public function index()
    {
        try {
            return view('admin.blogs.index');
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $blog = Blog::query()
                ->leftJoin('blog_categories as bc', 'bc.id', '=', 'blogs.category_id')
                ->select('blogs.*', 'bc.name as category')
                ->where('blogs.id', $id)
                ->first();

            if (!$blog) {
                return response()->json(['error' => 'Blog not found'], 404);
            }

            return response()->json(['data' => $blog], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


    public function create()
    {
        try {
            $categories = BlogCategory::where('status', 1)->get();
            return view('admin.blogs.create', compact('categories'));
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $blog = Blog::findOrFail(decryptId($id));
            $categories = BlogCategory::where('status', 1)->get();
            return view('admin.blogs.edit', compact('blog', 'categories'));
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataBlogs(Request $request)
    {
        try {
            if ($request->ajax()) {
                $blogs = Blog::query()
                    ->leftJoin('blog_categories as bc', 'bc.id', '=', 'blogs.category_id')
                    ->select('blogs.*', 'bc.name as category');
                if ($request->status !== null && $request->status !== '') {
                    $blogs->where('blogs.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $blogs->where('blogs.is_popular', $request->popular);
                }

                if ($request->featured !== null && $request->featured !== '') {
                    $blogs->where('blogs.featured', $request->featured);
                }

                if ($request->publish_date) {
                    $blogs->whereDate('blogs.publish_date', $request->publish_date);
                }

                if ($request->created_date) {
                    $blogs->whereDate('blogs.created_at', $request->created_date);
                }
                return DataTables::of($blogs)
                    ->addColumn('status', function ($b) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $b->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('featured', function ($b) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => 3,
                            'current_is_popular_priority_status' => $b->featured
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($b) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.blogs.edit', encryptId($b->id)),
                            'delete_id' => $b->id,
                            'current_status' => $b->status,
                            'current_is_popular_priority_status' => $b->featured,
                            'hidden_id' => $b->id,
                            'view_id' => $b->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->addColumn('icon', function ($b) {
                        if ($b->icon) {
                            return '<img src="' . asset('uploads/blogs/' . $b->icon) . '" style="max-width:80px;" />';
                        }
                        return '-';
                    })
                    ->rawColumns(['action', 'status', 'featured', 'icon'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        \Log::debug('Blog Store Request Data:', $request->all());

        try {
            $rules = [
                'category_id'  => 'required|exists:blog_categories,id',
                'title'        => 'required|string|max:200',
                'meta_title'   => 'nullable|string|max:255',
                'slug'         => 'required|string|max:200|unique:blogs,slug' . ($id ? ",$id" : ''),
                'excerpt'      => 'nullable|string',
                'content'      => 'nullable|string',
                'content_sections' => 'nullable|string',
                'read_time'    => 'nullable|string|max:50',
                'author'       => 'nullable|string|max:100',
                'author_email' => 'nullable|email|max:255',
                'publish_date' => 'nullable|date',
                'tags'         => 'nullable',
                'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'icon'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'featured_image_alt' => 'nullable|string|max:255',
                'featured'     => 'nullable|boolean',
                'status'       => 'nullable|boolean',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $icon = null;
            $imageKey = $request->hasFile('featured_image') ? 'featured_image' : ($request->hasFile('icon') ? 'icon' : null);
            if ($imageKey) {
                $blog = Blog::where('id', $id)->first();
                if ($blog) {
                    $filePath = public_path('uploads/blogs/' . $blog->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $icon = ImageUploadHelper::blogsimageUpload($request->file($imageKey));
            } elseif ($id != 0) {
                $icon = Blog::find($id)?->icon;
            }

            // Handle section images if any
            $content_sections = $request->input('content_sections', '[]');
            $sections = json_decode($content_sections, true);
            
            // If editing, get old sections for cleanup
            $oldSections = [];
            if ($id != 0) {
                $oldBlog = Blog::find($id);
                if ($oldBlog && $oldBlog->content_sections) {
                    $oldSections = json_decode($oldBlog->content_sections, true);
                }
            }

            if (is_array($sections)) {
                foreach ($sections as $key => $section) {
                    $fileKey = 'section_image_' . $key;
                    if ($section['type'] == 'image') {
                        if ($request->hasFile($fileKey)) {
                            // If there was an old image in this position (or matching by some ID), delete it
                            // Since it's a dynamic list, it's safer to compare images or just handle specific replaces
                            if (isset($oldSections[$key]['image']) && $oldSections[$key]['image']) {
                                $oldFilePath = public_path('uploads/blogs/' . $oldSections[$key]['image']);
                                if (File::exists($oldFilePath)) {
                                    File::delete($oldFilePath);
                                }
                            }
                            
                            $sectionImage = ImageUploadHelper::blogsimageUpload($request->file($fileKey));
                            $sections[$key]['image'] = $sectionImage;
                        }
                    }
                }
                $content_sections = json_encode($sections);
            }

            if ($request->filled('tags')) {
                $array = array_map('trim', explode(',', $request->tags));
                $array = array_filter($array, fn($val) => $val !== '');
                $tags = json_encode(array_values($array));
            }

            $data = [
                'category_id'  => $request->category_id,
                'title'        => $request->title,
                'meta_title'   => $request->meta_title,
                'slug'         => $request->slug,
                'excerpt'      => $request->excerpt,
                'content'      => $request->content,
                'content_sections' => $content_sections,
                'read_time'    => $request->read_time,
                'author'       => $request->author,
                'author_email' => $request->author_email,
                'publish_date' => $request->publish_date ?? today(),
                'tags'         => $tags ?? null,
                'icon'         => $icon,
                'featured_image_alt' => $request->featured_image_alt,
                'meta_keywords'    => $request->meta_keywords,
                'meta_description'    => $request->meta_description,
                'featured'     => (int) $request->input('featured', 0),
                'status'       => (int) $request->input('status', 1),
            ];

            if ($id == 0) {
                Blog::create($data);
                $msg = 'Blog added successfully';
            } else {
                Blog::where('id', $id)->update($data);
                $msg = 'Blog updated successfully';
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Throwable $e) {
            \Log::error('Blog Store Error: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            Blog::where('id', $id)->update(['status' => (int)$status]);
            return response()->json(['message' => 'Blog status updated']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeFeaturedStatus($id, $status)
    {
        try {
            Blog::where('id', $id)->update(['featured' => (int)$status]);
            return response()->json(['message' => 'Blog featured status updated']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::find($id);
            if ($blog) {
                // Delete Featured Image
                if ($blog->icon) {
                    $filePath = public_path('uploads/blogs/' . $blog->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                
                // Delete Section Images
                if ($blog->content_sections) {
                    $sections = json_decode($blog->content_sections, true);
                    if (is_array($sections)) {
                        foreach ($sections as $section) {
                            if ($section['type'] == 'image' && !empty($section['image'])) {
                                $sectionFile = public_path('uploads/blogs/' . $section['image']);
                                if (File::exists($sectionFile)) {
                                    File::delete($sectionFile);
                                }
                            }
                        }
                    }
                }
                
                $blog->delete();
            }
            return response()->json(['message' => 'Blog deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
