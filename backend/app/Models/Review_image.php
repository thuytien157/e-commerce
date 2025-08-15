<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review_image extends Model
{
    use HasFactory;
    protected $table = 'review_images';
    public $timestamps = false;
    protected $fillable = [
        'review_id',
        'image_url',
    ];
}
