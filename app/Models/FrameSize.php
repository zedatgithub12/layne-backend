<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameSize extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'frame_sizes';
    protected $fillable = ['frame_id', 'size_id', 'status'];


    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
