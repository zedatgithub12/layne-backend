<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'transaction_id',
        'currency',
        'gateway_fee',
        'refund_status',
        'refund_amount',
        'refund_date',
        'payment_date',
        'status'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
