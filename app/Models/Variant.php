<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'name',
        'color',
        'size',
        'price',
        'stock_quantity',
        'images',
        'ar_file',
        'availability',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'float',
        'stock_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
