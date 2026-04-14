<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class ProductCategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductCategoryController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.product-category.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.product-category.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $category = ProductCategory::where('id', decryptId($id))->first();
            if ($category) {
                return view('admin.product-category.edit', [
                    'category' => $category
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProductCategory(Request $request)
    {
        $function_name = 'getDataProductCategory';
        try {
            if ($request->ajax()) {
                $categories = DB::table('product_categories')->select('product_categories.*');

                if ($request->status !== null && $request->status !== '') {
                    $categories->where('product_categories.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $categories->where('product_categories.is_popular', $request->popular);
                }

                if ($request->created_date) {
                    $categories->whereDate('product_categories.created_at', $request->created_date);
                }

                return DataTables::of($categories)
                    ->addColumn('status', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $categories->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $categories->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($categories) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.product-category.edit', encryptId($categories->id)),
                            'delete_id' => $categories->id,
                            'current_status' => $categories->status,
                            'current_is_popular_priority_status' => $categories->is_popular,
                            'hidden_id' => $categories->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($categories) {
                        if ($categories->icon && file_exists(public_path('uploads/product-category/' . $categories->icon))) {
                            $imageUrl = asset('uploads/product-category/' . $categories->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Category Icon" />';
                        }
                        return '';
                    })

                    ->rawColumns(['action', 'icon', 'status', 'is_popular'])
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
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'name' => [
                    'required',
                    $id == 0 ? 'unique:product_categories,name' : 'unique:product_categories,name,' . $id . ',id',
                ],
                'icon' => $id == 0 ? 'image|mimes:jpeg,png,jpg,gif,svg,webp' : 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'name.required' => 'The category name is required.',
                'name.unique' => 'The category name has already been taken.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            ];


            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::productCategoryimageUpload($request->icon);
                }

                ProductCategory::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon ?? null,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Product category added successfully"
                ]);
            } else {
                $category = ProductCategory::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $filePath = public_path('uploads/product-category/' . $category->icon);

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $icon = ImageUploadHelper::productCategoryimageUpload($request->icon);
                } else {
                    $icon = $category->icon;
                }

                ProductCategory::where('id', $id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Product category edited successfully"
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            ProductCategory::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        $function_name = 'changePriorityStatus';
        try {
            ProductCategory::where('id', $id)->update(['is_popular' => $status]);
            return response()->json([
                'message' => trans('admin_string.msg_priority_status_change')
            ]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        $function_name = 'destroy';
        try {
            $category = ProductCategory::where('id', $id)->first();
            if ($category) {
                $filePath = public_path('uploads/product-category/' . $category->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $category->delete();

                return response()->json([
                    'message' => trans('admin_string.category_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Failed to delete the image from S3 or no category found.");
                return response()->json(['error' => 'Failed to delete the image from S3 or no category found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
