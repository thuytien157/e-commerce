<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    protected $table = 'variants';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'sku',
        'stock_quantity',
        'slug',
        'main_image_url'
    ];

    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attributes', 'variant_id', 'attribute_value_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function variantImages()
    {
        return $this->hasMany(VariantImage::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
