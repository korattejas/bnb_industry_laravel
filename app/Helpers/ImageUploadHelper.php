<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ImageUploadHelper
{
    /**
     * Common image upload logic
     */
    public static function upload($file, $folder)
    {
        if ($file) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/' . $folder), $filename);
            return $filename;
        }
        return null;
    }

    public static function productCategoryimageUpload($file)
    {
        return self::upload($file, 'product-category');
    }

    public static function productSubcategoryImageUpload($file)
    {
        return self::upload($file, 'product-subcategory');
    }

    public static function productimageUpload($file)
    {
        return self::upload($file, 'product');
    }

    public static function blogCategoryimageUpload($file)
    {
        return self::upload($file, 'blog-category');
    }

    public static function blogsimageUpload($file)
    {
        return self::upload($file, 'blogs');
    }

    public static function reviewCustomerImageUpload($file)
    {
        return self::upload($file, 'reviews');
    }

    public static function reviewImageUpload($file)
    {
        return self::upload($file, 'reviews');
    }

    public static function reviewVideoUpload($file)
    {
        // Actually same logic works for video if using move()
        return self::upload($file, 'reviews');
    }

    public static function homeCounterImageUpload($file)
    {
        return self::upload($file, 'home-counters');
    }

    public static function PortfolioImageUpload($file)
    {
        return self::upload($file, 'portfolio');
    }
}
