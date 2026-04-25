<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'city_name',
        'country_name',
        'country_code',
        'pincode',
        'product_id',
        'subject',
        'message',
        'status',
    ];
}
