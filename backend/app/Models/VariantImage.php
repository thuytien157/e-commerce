<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    use HasFactory;
    protected $table = 'variant_images';
    public $timestamps = false;
    protected $fillable = [
        'variant_id',
        'image_url'
    ];
}
