<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FramesShape extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'frames_shapes';

    protected $fillable = [
        'id',
        'frame_id',
        'shape_id',
        'status'
    ];

    // Defining relationships
    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function shape()
    {
        return $this->belongsTo(FrameShape::class);
    }
}
