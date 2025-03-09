<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'customer_id',
        'frame_id',
        'review_text',
        'rating_value',
        'rated_features',
        'is_featured',
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
}
