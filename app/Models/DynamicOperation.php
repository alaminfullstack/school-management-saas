<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'brand_title',
        'brand_description',
        'promotion_text',
        'brand_logo',
        'school_dashboard_logo',
        'brand_banner',
        'school_dashboard_banners'
    ];

    protected $casts = [
        'school_dashboard_banners' => 'array'
    ];
}
