<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;


class ProductController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.products.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $product = Product::leftJoin('product_categories as sc', 'sc.id', '=', 'products.category_id')
                ->leftJoin('product_subcategories as ssc', 'ssc.id', '=', 'products.sub_category_id')
                ->select('products.*', 'sc.name as category_name', 'ssc.name as sub_category_name')
                ->where('products.id', $id)
                ->first();

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            return response()->json(['data' => $product], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $subcategories = ProductSubcategory::where('status', 1)->select('name', 'id')->get();
            $categories = ProductCategory::where('status', 1)->select('name', 'id')->get();
            return view('admin.products.create', compact('categories', 'subcategories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $product = Product::findOrFail(decryptId($id));
            $categories = ProductCategory::where('status', 1)->select('name', 'id')->get();

            return view('admin.products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProduct(Request $request)
    {
        $function_name = 'getDataProduct';
        try {
            if ($request->ajax()) {
                $products = Product::query()
                    ->leftJoin('product_categories as sc', 'sc.id', '=', 'products.category_id')
                    ->select('products.*', 'sc.name as category_name');

                if ($request->status !== null && $request->status !== '') {
                    $products->where('products.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $products->where('products.is_popular', $request->popular);
                }

                if ($request->created_date) {
                    $products->whereDate('products.created_at', $request->created_date);
                }

                return DataTables::of($products)
                    ->addColumn('status', function ($s) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $s->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_popular', function ($s) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => 3,
                            'current_is_popular_priority_status' => $s->is_popular
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('images', function ($s) {
                        if (empty($s->images)) return '<span class="text-muted">No Images</span>';
                        $html = '<div class="photo-stack">';
                        $limit = 3;
                        $count = 0;
                        $total = count($s->images);
                        foreach ($s->images as $img) {
                            if ($count >= $limit) break;
                            $url = asset('uploads/product/' . $img);
                            $html .= '<img src="' . $url . '" class="photo-stack-item" title="Product Image" />';
                            $count++;
                        }
                        if ($total > $limit) {
                            $html .= '<div class="photo-count-badge">+' . ($total - $limit) . '</div>';
                        }
                        $html .= '</div>';
                        return $html;
                    })
                    ->addColumn('action', function ($s) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.product.edit', encryptId($s->id)),
                            'delete_id' => $s->id,
                            'current_status' => $s->status,
                            'current_is_popular_priority_status' => $s->is_popular,
                            'hidden_id' => $s->id,
                            'view_id' => $s->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status', 'is_popular', 'images'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = $request->all();
        try {
            $id = $request->input('edit_value', 0);

            $validateArray = [
                'category_id' => 'required|exists:product_categories,id',
                'sub_category_id' => 'nullable',
                'name'        => 'required',
                // 'price'       => 'required|numeric|min:0',
                // 'discount_price' => 'nullable|numeric|min:0',
                'description' => 'required|string',
                'content_sections' => 'nullable|string',
                'photos'   => 'nullable|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keyword' => 'nullable|string',
                'product_brochure_photo' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:10240',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $storedImages = [];
            $product = null;

            if ($id != 0) {
                $product = Product::find($id);
                if ($product && is_array($product->images)) {
                    $storedImages = $product->images;
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $image) {
                    $filename = ImageUploadHelper::productimageUpload($image);
                    $storedImages[] = $filename;
                }
            }

            $product_brochure_photo = $product ? $product->product_brochure_photo : null;
            if ($request->hasFile('product_brochure_photo')) {
                if ($product_brochure_photo) {
                    $oldBrochurePath = public_path('uploads/product-brochure/' . $product_brochure_photo);
                    if (File::exists($oldBrochurePath)) {
                        File::delete($oldBrochurePath);
                    }
                }
                $product_brochure_photo = ImageUploadHelper::productBrochureUpload($request->file('product_brochure_photo'));
            }

            $includes = null;

            if ($request->filled('includes')) {
                $array = array_map('trim', explode(',', $request->includes));

                $array = array_filter($array, fn($val) => $val !== '');

                $includes = json_encode(array_values($array));
            }

            // Handle section images if any
            $content_sections_string = $request->input('content_sections', '[]');
            $sections = json_decode($content_sections_string, true);
            if (!is_array($sections)) {
                $sections = [];
            }

            // If editing, get old sections for cleanup
            $oldSections = [];
            if ($id != 0 && $product) {
                $oldSections = is_array($product->content_sections) ? $product->content_sections : json_decode($product->content_sections, true);
                if (!is_array($oldSections)) {
                    $oldSections = [];
                }
            }

            foreach ($sections as $key => $section) {
                $fileKey = 'section_image_' . $key;
                if (isset($section['type']) && $section['type'] == 'image') {
                    if ($request->hasFile($fileKey)) {
                        if (isset($oldSections[$key]['image']) && $oldSections[$key]['image']) {
                            $oldFilePath = public_path('uploads/product/' . $oldSections[$key]['image']);
                            if (File::exists($oldFilePath)) {
                                File::delete($oldFilePath);
                            }
                        }

                        $sectionImage = ImageUploadHelper::productimageUpload($request->file($fileKey));
                        $sections[$key]['image'] = $sectionImage;
                    } elseif (isset($oldSections[$key]['image'])) {
                        $sections[$key]['image'] = $oldSections[$key]['image'];
                    }
                }
            }
            $content_sections = $sections;

            $data = [
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'name'        => $request->name,
                'watt'        => $request->watt,
                'price'       => $request->price,
                'description' => $request->description,
                'content_sections' => $content_sections,
                'includes'    => $includes,
                'images'      => !empty($storedImages) ? $storedImages : null,
                'is_popular'  => (int) $request->is_popular,
                'status'      => (int) $request->status,
                'meta_title'  => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keyword' => $request->meta_keyword,
                'product_brochure_photo' => $product_brochure_photo,
            ];

            if ($id == 0) {
                Product::create($data);
                return response()->json(['success' => true, 'message' => "Product added successfully"]);
            } else {
                if ($product) {
                    $product->update($data);
                    return response()->json(['success' => true, 'message' => "Product updated successfully"]);
                }
                return response()->json(['success' => false, 'message' => "Product not found"], 404);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            Product::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        try {
            Product::where('id', $id)->update(['is_popular' => $status]);
            return response()->json(['message' => trans('admin_string.msg_priority_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }


    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);
            if ($product) {
                if (!empty($product->images) && is_array($product->images)) {
                    foreach ($product->images as $image) {
                        $filePath = public_path('uploads/product/' . $image);
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                        }
                    }
                }

                if ($product->product_brochure_photo) {
                    $brochurePath = public_path('uploads/product-brochure/' . $product->product_brochure_photo);
                    if (File::exists($brochurePath)) {
                        File::delete($brochurePath);
                    }
                }

                // Delete Section Images
                if ($product->content_sections) {
                    $sections = is_array($product->content_sections) ? $product->content_sections : json_decode($product->content_sections, true);
                    if (is_array($sections)) {
                        foreach ($sections as $section) {
                            if ($section['type'] == 'image' && !empty($section['image'])) {
                                $sectionFile = public_path('uploads/product/' . $section['image']);
                                if (File::exists($sectionFile)) {
                                    File::delete($sectionFile);
                                }
                            }
                        }
                    }
                }

                $product->delete();
                return response()->json(['message' => trans('admin_string.product_deleted_successfully')]);
            }
            return response()->json(['error' => 'Product not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function removeImage(Request $request)
    {
        try {
            $id = $request->id;
            $imageName = $request->image;

            $product = Product::find($id);
            if ($product && is_array($product->images)) {
                $images = $product->images;

                // Remove the image name from the array
                if (($key = array_search($imageName, $images)) !== false) {
                    unset($images[$key]);

                    // Reset array keys to avoid index issues
                    $images = array_values($images);

                    // Update the product
                    $product->update(['images' => $images]);

                    // Delete the physical file
                    $path = public_path('uploads/product/' . $imageName);
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    return response()->json(['success' => true, 'message' => 'Image removed successfully']);
                }
            }
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportPdf()
    {
        try {
            $products = Product::leftJoin('product_categories as sc', 'sc.id', '=', 'products.category_id')
                ->leftJoin('product_subcategories as ssc', 'ssc.id', '=', 'products.sub_category_id')
                ->select('products.*', 'sc.name as category_name', 'ssc.name as sub_category_name')
                ->get();

            $pdf = Pdf::loadView('admin.products.export-pdf', compact('products'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('products_list.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export PDF');
        }
    }

    public function exportExcel()
    {
        try {
            return Excel::download(new ProductsExport, 'products_list.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export Excel');
        }
    }

    public function getSubcategories($categoryId)
    {
        try {
            $subcategories = ProductSubcategory::where('product_category_id', $categoryId)
                ->where('status', 1)
                ->get();

            return response()->json($subcategories);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sub category data');
        }
    }
}
