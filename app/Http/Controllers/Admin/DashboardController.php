<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\CustomerReview;
use App\Models\ContactSubmission;
// use App\Models\Hiring;
 
class DashboardController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;
 
    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/DashboardController";
    }
 
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $function_name = 'index';
        try {
            $totalContacts = ContactSubmission::count();
            $totalProductCategory = ProductCategory::where('status', 1)->count();
            $totalProducts = Product::where('status', 1)->count();
            $totalBlogs = Blog::where('status', 1)->count();
            $totalBlogCategory = BlogCategory::where('status', 1)->count();
// $totalHirings = Hiring::where('status', 1)->count();
            $totalCustomerReviews = CustomerReview::where('status', 1)->count();
 
            return view('admin.dashboard.index', [
                'totalContacts'          => $totalContacts,
                'totalProductCategory'   => $totalProductCategory,
                'totalProducts'          => $totalProducts,
                'totalBlogs'             => $totalBlogs,
                'totalBlogCategory'      => $totalBlogCategory,
// 'totalHirings'           => $totalHirings,
                'totalCustomerReviews'   => $totalCustomerReviews,
            ]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
