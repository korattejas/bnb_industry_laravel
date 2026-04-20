<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ProductController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        parent::__construct();
        $this->controller_name = 'API/ProductController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getProductCategory(): JsonResponse
    {
        $function_name = 'getProductCategory';
        try {
            $categories = DB::table('product_categories as c')
                ->select(
                    'c.id',
                    'c.name',
                    DB::raw('CONCAT("' . asset('uploads/product-category') . '/", c.icon) AS icon'),
                    'c.description',
                    'c.is_popular'
                )
                ->where('c.status', 1)
                ->orderByDesc('c.is_popular')
                ->get();

            if ($categories->isEmpty()) {
                return $this->sendError('No category found.', $this->backend_error_status);
            }

            $categories->transform(function ($category) {
                $category->is_popular = (int) $category->is_popular;
                return $category;
            });

            return $this->sendResponse(
                $categories,
                'Categories with subcategories retrieved successfully',
                $this->success_status
            );

        } catch (Exception $e) {

            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }


    public function getProducts(Request $request): JsonResponse
    {
        $function_name = 'getProducts';

        try {
            // Updated selection fields (removed deleted fields: duration, rating, reviews and changed icon to images)
            $selectFields = [
                's.id',
                's.category_id',
                'c.name as category_name',
                's.name',
                's.watt',
                's.price',
                's.description',
                's.content_sections',
                's.includes',
                's.images',
                's.is_popular'
            ];

            // 1. If product_id is provided, show single product details and related products from same category
            if ($request->filled('product_id')) {
                $product = DB::table('products as s')
                    ->join('product_categories as c', 's.category_id', '=', 'c.id')
                    ->select($selectFields)
                    ->where('s.id', $request->product_id)
                    ->where('s.status', 1)
                    ->first();

                if (!$product) {
                    return $this->sendError('Product not found.', $this->backend_error_status);
                }

                // Process includes and images for the single product
                $product->includes = $product->includes ? json_decode($product->includes, true) : [];
                $product->content_sections = $product->content_sections ? json_decode($product->content_sections, true) : [];
                if (is_array($product->content_sections)) {
                    foreach ($product->content_sections as &$section) {
                        if ($section['type'] == 'image' && !empty($section['image'])) {
                            $section['image'] = asset('uploads/product/' . $section['image']);
                        }
                    }
                }

                $productImages = $product->images ? json_decode($product->images, true) : [];
                $product->images = array_map(function ($img) {
                    return asset('uploads/product/' . $img);
                }, (array)$productImages);
                $product->is_popular = (int) $product->is_popular;

                // 2. Fetch Related Products from same category (excluding current product)
                $relatedProducts = DB::table('products as s')
                    ->join('product_categories as c', 's.category_id', '=', 'c.id')
                    ->select($selectFields)
                    ->where('s.category_id', $product->category_id)
                    ->where('s.id', '!=', $product->id)
                    ->where('s.status', 1)
                    ->orderByDesc('s.id')
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        $item->includes = $item->includes ? json_decode($item->includes, true) : [];
                        $itemImages = $item->images ? json_decode($item->images, true) : [];
                        $item->images = array_map(function ($img) {
                            return asset('uploads/product/' . $img);
                        }, (array)$itemImages);
                        $item->is_popular = (int) $item->is_popular;
                        return $item;
                    });

                return $this->sendResponse(
                    [
                        'product_details' => $product,
                        'related_products' => $relatedProducts
                    ],
                    'Product details retrieved successfully',
                    $this->success_status
                );
            }

            // 3. Normal products listing (with filters and search)
            $query = DB::table('products as s')
                ->join('product_categories as c', 's.category_id', '=', 'c.id')
                ->select($selectFields)
                ->where('s.status', 1);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('s.name', 'like', "%$search%")
                        ->orWhere('s.description', 'like', "%$search%")
                        ->orWhere('c.name', 'like', "%$search%")
                        ->orWhere('s.price', 'like', "%$search%")
                        ->orWhere('s.includes', 'like', "%$search%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('s.category_id', $request->category_id);
            }

            $perPage = $request->per_page ?? 24;
            $page = $request->page ?? 1;

            $products = $query->orderByDesc('s.id')
                ->paginate($perPage, ['*'], 'page', $page)
                ->through(function ($product) {
                    $product->includes = $product->includes ? json_decode($product->includes, true) : [];
                    $product->content_sections = $product->content_sections ? json_decode($product->content_sections, true) : [];
                    if (is_array($product->content_sections)) {
                        foreach ($product->content_sections as &$section) {
                            if ($section['type'] == 'image' && !empty($section['image'])) {
                                $section['image'] = asset('uploads/product/' . $section['image']);
                            }
                        }
                    }

                    $productImages = $product->images ? json_decode($product->images, true) : [];
                    $product->images = array_map(function ($img) {
                        return asset('uploads/product/' . $img);
                    }, (array)$productImages);
                    $product->is_popular = (int) $product->is_popular;
                    return $product;
                });

            if ($products->total() === 0) {
                return $this->sendError('No product found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $products,
                'Products retrieved successfully',
                $this->success_status
            );
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }
}
