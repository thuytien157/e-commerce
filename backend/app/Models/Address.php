<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';
    public $timestamps = false;
    protected $fillable = [
        'customer_id',
        'customer_name',
        'phone',
        'address',
        'default',
        'province_id',
        'district_id',
        'ward_code',
    ];
    protected $casts = [
        'default' => 'boolean',
    ];
}
