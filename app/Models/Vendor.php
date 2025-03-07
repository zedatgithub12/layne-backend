<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'address', 'phone', 'email'];
    public $incrementing = false;
    protected $keyType = 'string';
}
