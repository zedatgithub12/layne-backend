<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameLens extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'frame_id',
        'lens_id',
        'is_default',
        'status',
    ];

    /**
     * Relationship with Frame.
     */
    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    /**
     * Relationship with Lens.
     */
    public function lens()
    {
        return $this->belongsTo(Lens::class);
    }
}
