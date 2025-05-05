<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'categories',
        'shape',
        'lens_types',
        'description',
        'product_weight',
        'sku',
        'material',
        'pd_range',
        'rx_range',
        'spring_hinge',
        'bridge_fit',
        'adjustable_nose_pad',
        'is_flexible',
        'need_prescription',
        'tags',
        'status'
    ];

    protected $casts = [
        'categories' => 'array',
        'lens_types' => 'array',
        'tags' => 'array',
        'adjustable_nose_pad' => 'boolean',
        'is_flexible' => 'boolean',
        'need_prescription' => 'boolean',

    ];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }


}
