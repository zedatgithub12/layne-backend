<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'brand',
        'description',
        'category_id',
        'weight',
        'gender',
        'price',
        'discount_price',
        'stock_quantity',
        'featured',
        'images',
        'tags',
        'ratings',
        'status',
        'try_on_asset',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'ratings' => 'float',
        'featured' => 'boolean',
    ];
}
