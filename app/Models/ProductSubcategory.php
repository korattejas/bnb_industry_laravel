<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'name',
        'icon',
        'description',
        'is_popular',
        'status',
    ];

    /**
     * Relation with ProductCategory
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
