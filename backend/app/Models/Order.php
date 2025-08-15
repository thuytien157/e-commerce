<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    public $timestamps = false;
    protected $fillable = [
        'shipping_address_id',
        'customer_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_address',
        'total_amount',
        'shipping_money',
        'order_date',
        'cancellation_reason'
    ];

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
}
