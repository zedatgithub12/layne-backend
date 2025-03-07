<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lens extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'lens_type_id',
        'lens_material',
        'lens_color',
        'lens_coating',
        'lens_power',
        'polarized',
        'photochromatic',
        'lens_thickness',
        'description',
        'use_cases',
        'status'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'polarized' => 'boolean',
        'photochromatic' => 'boolean',
        'status' => 'string',
    ];

    // Relationship with LensType
    public function lensType()
    {
        return $this->belongsTo(LensType::class, 'lens_type_id');
    }
}
