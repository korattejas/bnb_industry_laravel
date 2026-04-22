<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'watt',
        'price',
        'discount_price',
        'description',
        'content_sections',
        'includes',
        'images',
        'is_popular',
        'status',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'product_brochure_photo',
    ];

    protected $casts = [
        'images' => 'array',
        'content_sections' => 'array'
    ];
}
