<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'meta_title',
        'slug',
        'excerpt',
        'content',
        'content_sections',
        'read_time',
        'author',
        'author_email',
        'publish_date',
        'tags',
        'icon',
        'featured_image_alt',
        'featured',
        'meta_keywords',
        'meta_description',
        'status',
    ];
}
