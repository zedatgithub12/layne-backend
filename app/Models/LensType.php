<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LensType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description', 'use_case', 'thickness', 'status'];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'status' => 'string',
    ];
}
