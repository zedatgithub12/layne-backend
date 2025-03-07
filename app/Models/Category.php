<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'slug', 'status'];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'status' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        // Generate slug from name before saving
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
