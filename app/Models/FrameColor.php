<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameColor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['frame_id', 'color_id', 'status'];

    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
