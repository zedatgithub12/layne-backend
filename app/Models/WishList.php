<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'frame_id'
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
