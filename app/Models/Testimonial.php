<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, HasUuids;


    protected $fillable = [
        'customer_id',
        'frame_id',
        'order_id',
        'image',
        'testimonial_text',
        'rating',
        'is_verified',
        'is_featured',
        'reply',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function frame()
    {
        return $this->belongsTo(Frame::class, 'frame_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
