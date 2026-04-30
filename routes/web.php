<?php

use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ContactSubmissionsController;
// use App\Http\Controllers\Admin\CustomerReviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
// use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\Admin\HomeCounterController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PoliciesController;
// use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductSubcategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Middleware\AdminCheck;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::get('logs/bnbindustry@gmail.com/8998', [LogViewerController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});





/* Admin Route */
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [LoginController::class, 'index'])->name('admin.login');
    Route::post('login-check', [LoginController::class, 'loginCheck'])->name('admin.login-check');

    Route::group(['middleware' => [AdminCheck::class]], function () {
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');




        /* Products Category Route */
        Route::get('product-category', [ProductCategoryController::class, 'index'])->name('admin.product-category.index');
        Route::get('product-category/create', [ProductCategoryController::class, 'create'])->name('admin.product-category.create');
        Route::post('product-category/store', [ProductCategoryController::class, 'store']);
        Route::delete('product-category/{id}', [ProductCategoryController::class, 'destroy']);
        Route::get('product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('admin.product-category.edit');
        Route::get('getDataProductCategory', [ProductCategoryController::class, 'getDataProductCategory'])->name('getDataProductCategory');
        Route::get('product-category/status/{id}/{status}', [ProductCategoryController::class, 'changeStatus']);
        Route::get('product-category/priority-status/{id}/{status}', [ProductCategoryController::class, 'changePriorityStatus']);

        /* Products Subcategory Route */
        Route::get('product-subcategory', [ProductSubcategoryController::class, 'index'])->name('admin.product-subcategory.index');
        Route::get('product-subcategory/create', [ProductSubcategoryController::class, 'create'])->name('admin.product-subcategory.create');
        Route::post('product-subcategory/store', [ProductSubcategoryController::class, 'store']);
        Route::delete('product-subcategory/{id}', [ProductSubcategoryController::class, 'destroy']);
        Route::get('product-subcategory/edit/{id}', [ProductSubcategoryController::class, 'edit'])->name('admin.product-subcategory.edit');
        Route::get('getDataProductSubcategory', [ProductSubcategoryController::class, 'getDataProductSubcategory'])->name('getDataProductSubcategory');
        Route::get('product-subcategory/status/{id}/{status}', [ProductSubcategoryController::class, 'changeStatus']);
        Route::get('product-subcategory/priority-status/{id}/{status}', [ProductSubcategoryController::class, 'changePriorityStatus']);


        /* Portfoio 
        Route::get('portfolio', [PortfolioController::class, 'index'])->name('admin.portfolio.index');
        Route::get('portfolio/create', [PortfolioController::class, 'create'])->name('admin.portfolio.create');
        Route::post('portfolio/store', [PortfolioController::class, 'store'])->name('admin.portfolio.store');
        Route::get('portfolio/edit/{id}', [PortfolioController::class, 'edit'])->name('admin.portfolio.edit');
        Route::delete('portfolio/{id}', [PortfolioController::class, 'destroy'])->name('admin.portfolio.destroy');
        Route::get('getDataPortfolio', [PortfolioController::class, 'getDataPortfolio'])->name('getDataPortfolio');
        Route::get('portfolio/status/{id}/{status}', [PortfolioController::class, 'changeStatus'])->name('admin.portfolio.changeStatus');
        Route::post('portfolio/remove-image', [PortfolioController::class, 'removeImage'])->name('admin.portfolio.removeImage');
        */
    

        /* Blog Category Route */
        Route::get('blog-category', [BlogCategoryController::class, 'index'])->name('admin.blog-category.index');
        Route::get('blog-category/create', [BlogCategoryController::class, 'create'])->name('admin.blog-category.create');
        Route::post('blog-category/store', [BlogCategoryController::class, 'store']);
        Route::delete('blog-category/{id}', [BlogCategoryController::class, 'destroy']);
        Route::get('blog-category/edit/{id}', [BlogCategoryController::class, 'edit'])->name('admin.blog-category.edit');
        Route::get('getDataBlogCategory', [BlogCategoryController::class, 'getDataBlogCategory'])->name('getDataBlogCategory');
        Route::get('blog-category/status/{id}/{status}', [BlogCategoryController::class, 'changeStatus']);
        Route::get('blog-category/priority-status/{id}/{status}', [BlogCategoryController::class, 'changePriorityStatus']);

        /* Products Route */
        Route::get('product', [ProductController::class, 'index'])->name('admin.product.index');
        Route::get('product/create', [ProductController::class, 'create'])->name('admin.product.create');
        Route::post('product/store', [ProductController::class, 'store']);
        Route::delete('product/{id}', [ProductController::class, 'destroy']);
        Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('admin.product.edit');
        Route::get('getDataProduct', [ProductController::class, 'getDataProduct'])->name('getDataProduct');
        Route::get('product/status/{id}/{status}', [ProductController::class, 'changeStatus']);
        Route::get('product/priority-status/{id}/{status}', [ProductController::class, 'changePriorityStatus']);
        Route::get('product-view/{id}', [ProductController::class, 'view']);
        Route::get('product/export-pdf', [ProductController::class, 'exportPdf'])->name('admin.product.export.pdf');
        Route::get('product/export-excel', [ProductController::class, 'exportExcel'])->name('admin.product.export.excel');
        Route::get('product/get-subcategories/{categoryId}', [ProductController::class, 'getSubcategories']);
        Route::post('product/remove-image', [ProductController::class, 'removeImage'])->name('admin.product.removeImage');


        Route::get('product/get-subcategories/{categoryId}', [ProductController::class, 'getSubcategories']);
        Route::post('product/remove-image', [ProductController::class, 'removeImage'])->name('admin.product.removeImage');



        /* 
        // Customer Reviews
        Route::get('reviews', [CustomerReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('reviews/create', [CustomerReviewController::class, 'create'])->name('admin.reviews.create');
        Route::post('reviews/store', [CustomerReviewController::class, 'store'])->name('admin.reviews.store');
        Route::get('reviews/edit/{id}', [CustomerReviewController::class, 'edit'])->name('admin.reviews.edit');
        Route::delete('reviews/{id}', [CustomerReviewController::class, 'destroy'])->name('admin.reviews.destroy');
        Route::get('getDataReviews', [CustomerReviewController::class, 'getDataReviews'])->name('getDataReviews');
        Route::get('reviews/status/{id}/{status}', [CustomerReviewController::class, 'changeStatus'])->name('admin.reviews.changeStatus');
        Route::get('reviews/priority-status/{id}/{status}', [CustomerReviewController::class, 'changePopularStatus'])->name('admin.reviews.changePopularStatus');
        Route::get('reviews-view/{id}', [CustomerReviewController::class, 'view']);
        */

        // Blogs
        Route::get('blogs', [BlogController::class, 'index'])->name('admin.blogs.index');
        Route::get('blogs/create', [BlogController::class, 'create'])->name('admin.blogs.create');
        Route::get('blogs/edit/{id}', [BlogController::class, 'edit'])->name('admin.blogs.edit');
        Route::post('blogs/store', [BlogController::class, 'store'])->name('admin.blogs.store');
        Route::get('getDataBlogs', [BlogController::class, 'getDataBlogs'])->name('admin.blogs.getDataBlogs');
        Route::delete('blogs/{id}', [BlogController::class, 'destroy'])->name('admin.blogs.destroy');
        Route::get('blogs/status/{id}/{status}', [BlogController::class, 'changeStatus'])->name('admin.blogs.changeStatus');
        Route::get('blogs/priority-status/{id}/{status}', [BlogController::class, 'changeFeaturedStatus'])->name('admin.blogs.changeFeaturedStatus');
        Route::get('blogs-view/{id}', [BlogController::class, 'view']);

        /* 
        // Hirings
        Route::get('hirings', [HiringController::class, 'index'])->name('admin.hirings.index');
        Route::get('hirings/create', [HiringController::class, 'create'])->name('admin.hirings.create');
        Route::post('hirings/store', [HiringController::class, 'store'])->name('admin.hirings.store');
        Route::get('hirings/edit/{id}', [HiringController::class, 'edit'])->name('admin.hirings.edit');
        Route::delete('hirings/{id}', [HiringController::class, 'destroy'])->name('admin.hirings.destroy');
        Route::get('getDataHirings', [HiringController::class, 'getDataHirings'])->name('getDataHirings');
        Route::get('hirings/status/{id}/{status}', [HiringController::class, 'changeStatus'])->name('admin.hirings.changeStatus');
        Route::get('hirings/priority-status/{id}/{status}', [HiringController::class, 'changePopularStatus'])->name('admin.hirings.changePopularStatus');
        Route::get('hirings-view/{id}', [HiringController::class, 'view']);
        */

        /* Home Counters Routes */
        Route::get('home-counters', [HomeCounterController::class, 'index'])->name('admin.home-counters.index');
        Route::get('home-counters/create', [HomeCounterController::class, 'create'])->name('admin.home-counters.create');
        Route::post('home-counters/store', [HomeCounterController::class, 'store'])->name('admin.home-counters.store');
        Route::delete('home-counters/{id}', [HomeCounterController::class, 'destroy'])->name('admin.home-counters.destroy');
        Route::get('home-counters/edit/{id}', [HomeCounterController::class, 'edit'])->name('admin.home-counters.edit');
        Route::get('getDataHomeCounters', [HomeCounterController::class, 'getDataHomeCounters'])->name('admin.home-counters.data');
        Route::get('home-counters/status/{id}/{status}', [HomeCounterController::class, 'changeStatus'])->name('admin.home-counters.status');
        Route::get('home-counters/priority-status/{id}/{status}', [HomeCounterController::class, 'changePriorityStatus'])->name('admin.home-counters.priority-status');

        /* FAQs Routes */
        Route::get('faqs', [FaqController::class, 'index'])->name('admin.faqs.index');
        Route::get('faqs/create', [FaqController::class, 'create'])->name('admin.faqs.create');
        Route::post('faqs/store', [FaqController::class, 'store'])->name('admin.faqs.store');
        Route::get('faqs/edit/{id}', [FaqController::class, 'edit'])->name('admin.faqs.edit');
        Route::post('faqs/update/{id}', [FaqController::class, 'update'])->name('admin.faqs.update');
        Route::delete('faqs/{id}', [FaqController::class, 'destroy'])->name('admin.faqs.destroy');
        Route::get('getDataFaqs', [FaqController::class, 'getDataFaqs'])->name('admin.faqs.data');
        Route::get('faqs/status/{id}/{status}', [FaqController::class, 'changeStatus'])->name('admin.faqs.status');

        /* Setting Route */
        Route::get('setting', [SettingController::class, 'index'])->name('admin.setting.index');
        Route::get('setting/create', [SettingController::class, 'create'])->name('admin.setting.create');
        Route::post('setting/store', [SettingController::class, 'store']);
        Route::delete('setting/{id}', [SettingController::class, 'destroy']);
        Route::get('setting/edit/{id}', [SettingController::class, 'edit'])->name('admin.setting.edit');
        Route::get('getDataSetting', [SettingController::class, 'getDataSetting'])->name('getDataSetting');
        Route::get('setting/status/{id}/{status}', [SettingController::class, 'changeStatus']);

        /* Contact Submissions Route */
        Route::get('contact-submissions', [ContactSubmissionsController::class, 'index'])->name('admin.contact-submissions.index');
        Route::get('getDataContactSubmissions', [ContactSubmissionsController::class, 'getDataContactSubmissions'])->name('admin.contact-submissions.data');
        Route::get('contact-submissions/status/{id}/{status}', [ContactSubmissionsController::class, 'changeStatus'])->name('admin.contact-submissions.status');
        Route::delete('contact-submissions/{id}', [ContactSubmissionsController::class, 'destroy'])->name('admin.contact-submissions.destroy');
        Route::get('contact-submissions-view/{id}', [ContactSubmissionsController::class, 'view'])->name('admin.contact-submissions.view');





        /* Policies Route */
        Route::get('policies', [PoliciesController::class, 'createOrUpdate'])->name('admin.policies.index');
        Route::post('policies/store', [PoliciesController::class, 'store']);

        /* Clients Routes */
        Route::get('clients', [ClientController::class, 'index'])->name('admin.clients.index');
        Route::get('clients/create', [ClientController::class, 'create'])->name('admin.clients.create');
        Route::post('clients/store', [ClientController::class, 'store'])->name('admin.clients.store');
        Route::get('clients/edit/{id}', [ClientController::class, 'edit'])->name('admin.clients.edit');
        Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');
        Route::get('getDataClients', [ClientController::class, 'getDataClients'])->name('admin.clients.data');
        Route::get('clients/status/{id}/{status}', [ClientController::class, 'changeStatus'])->name('admin.clients.status');
    });
});
