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
        'user_id',
        'order_number',
        'frame_id',
        'frame_name',
        'variant_id',
        'lens',
        'lens_type',
        'lens_variant_name',
        'lens_variant_value',
        'need_prescription',
        'prescription',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function frame()
    {
        return $this->belongsTo(Product::class, 'frame_id', 'id');
    }

    public function variant()
    {
        return $this->hasOne(Variant::class, 'id', 'variant_id');
    }


}

