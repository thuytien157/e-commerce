<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'status'
    ];

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function variants(){
        return $this->hasMany(Variant::class);
    }

    public function orderItems()
    {
        return $this->hasManyThrough(
            OrderItem::class,  // bảng đích
            Variant::class,    // bảng trung gian
            'product_id',      // khóa ngoại ở variants trỏ tới products
            'variant_id',      // khóa ngoại ở order_items trỏ tới variants
            'id',              // khóa chính ở products
            'id'               // khóa chính ở variants
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


}
