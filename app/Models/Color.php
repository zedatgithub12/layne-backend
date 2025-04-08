<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'color_code',
        'description',
        'is_textured',
        'texture_image',
        'is_mixed',
        'mixed_colors',
        'tags',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'tags' => 'array',
        'mixed_colors' => 'array',
    ];
}
