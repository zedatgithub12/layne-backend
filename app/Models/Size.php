<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'shorter_name',
        'width_range',
        'description',
        'tags',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
        'tags' => 'array',
    ];
}
