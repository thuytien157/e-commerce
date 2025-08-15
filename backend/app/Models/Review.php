<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews';
    public $timestamps = false;
    protected $fillable = [
        'customer_id',
        'product_id',
        'reply_customer_id',
        'reply_text',
        'rating',
        'comment',
        'created_at',
    ];

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function admin(){
        return $this->belongsTo(User::class, 'reply_customer_id');
    }

    public function images(){
        return $this->hasMany(Review_image::class);
    }

}
