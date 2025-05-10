<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'status'
    ];
}
