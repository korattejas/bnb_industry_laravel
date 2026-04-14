<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSubcategory;
use App\Models\ProductCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class ProductSubcategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductSubcategoryController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.product-subcategory.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $categories = ProductCategory::where('status', 1)->get();
            return view('admin.product-subcategory.create', compact('categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $subcategory = ProductSubcategory::where('id', decryptId($id))->first();
            $categories = ProductCategory::where('status', 1)->get();
            if ($subcategory) {
                return view('admin.product-subcategory.edit', [
                    'subcategory' => $subcategory,
                    'categories' => $categories
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProductSubcategory(Request $request)
    {
        $function_name = 'getDataProductSubcategory';
        try {
            if ($request->ajax()) {
                $subcategories = DB::table('product_subcategories')
                    ->join('product_categories', 'product_categories.id', '=', 'product_subcategories.product_category_id')
                    ->select('product_subcategories.*', 'product_categories.name as category_name');

                if ($request->status !== null && $request->status !== '') {
                    $subcategories->where('product_subcategories.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $subcategories->where('product_subcategories.is_popular', $request->popular);
                }

                if ($request->created_date) {
                    $subcategories->whereDate('product_subcategories.created_at', $request->created_date);
                }

                return DataTables::of($subcategories)
                    ->addColumn('status', function ($sub) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $sub->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($sub) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $sub->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($sub) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.product-subcategory.edit', encryptId($sub->id)),
                            'delete_id' => $sub->id,
                            'current_status' => $sub->status,
                            'current_is_popular_priority_status' => $sub->is_popular,
                            'hidden_id' => $sub->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($sub) {
                        if ($sub->icon && file_exists(public_path('uploads/product-subcategory/' . $sub->icon))) {
                            $imageUrl = asset('uploads/product-subcategory/' . $sub->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Subcategory Icon" />';
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
                'product_category_id' => 'required|exists:product_categories,id',
                'name' => 'required',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'product_category_id.required' => 'The parent category is required.',
                'product_category_id.exists' => 'The selected category is invalid.',
                'name.required' => 'The subcategory name is required.',
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
                    $icon = ImageUploadHelper::productSubcategoryImageUpload($request->icon);
                }

                ProductSubcategory::create([
                    'product_category_id' => $request->product_category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon ?? null,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Product subcategory added successfully"
                ]);
            } else {
                $subcategory = ProductSubcategory::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $filePath = public_path('uploads/product-subcategory/' . $subcategory->icon);

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $icon = ImageUploadHelper::productSubcategoryImageUpload($request->icon);
                } else {
                    $icon = $subcategory->icon;
                }

                $subcategory->update([
                    'product_category_id' => $request->product_category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Product subcategory edited successfully"
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
            ProductSubcategory::where('id', $id)->update(['status' => $status]);
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
            ProductSubcategory::where('id', $id)->update(['is_popular' => $status]);
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
            $subcategory = ProductSubcategory::where('id', $id)->first();
            if ($subcategory) {
                $filePath = public_path('uploads/product-subcategory/' . $subcategory->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $subcategory->delete();

                return response()->json([
                    'message' => trans('admin_string.subcategory_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: No subcategory found.");
                return response()->json(['error' => 'No subcategory found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
