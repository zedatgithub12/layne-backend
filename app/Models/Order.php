<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'order_number',
        'total_price',
        'shipping_address',
        'shipping_method',
        'payment_status',
        'delivery_confirmation_code',
        'delivery_status',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_number = 'ORD-' . strtoupper(Str::random(10));
            $model->delivery_confirmation_code = 'CONF-' . strtoupper(Str::random(8));
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
