<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameShape extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'rim_type',
        'bridge_width',
        'temple_length',
        'lens_width',
        'frame_material',
        'face_shape_suitability',
        'status',
    ];

    protected $casts = [
        'bridge_width' => 'integer',
        'temple_length' => 'integer',
        'lens_width' => 'integer',
        'status' => 'string',
        'frame_material' => 'string',
    ];

}
