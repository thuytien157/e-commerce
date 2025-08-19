<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $table = 'attribute_values';
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'value_name'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

     public function variants()
    {
        return $this->belongsToMany(Variant::class, 'variant_attributes', 'attribute_value_id', 'variant_id');
    }
}
