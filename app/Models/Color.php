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
        'hex_code',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
