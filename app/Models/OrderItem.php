<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'frame_id',
        'frame_name',
        'lens_id',
        'color_id',
        'size_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'id' => 'string',
        'order_id' => 'string',
        'frame_id' => 'string',
        'lens_id' => 'string',
        'color_id' => 'string',
        'size_id' => 'string',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function lens()
    {
        return $this->belongsTo(Lens::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
