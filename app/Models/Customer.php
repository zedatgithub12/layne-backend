<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'avatar',
        'name',
        'gender',
        'birthdate',
        'phone_number',
        'email',
        'otp_code',
        'otp_expires_at',
        'is_verified',
        'shipping_address',
        'status'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'is_verified' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

}
