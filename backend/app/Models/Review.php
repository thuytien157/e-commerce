<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews';
    protected $fillable = [
        'customer_id',
        'product_id',
        'reply_customer_id',
        'reply_text',
        'rating',
        'comment',
        'created_at',
    ];
    protected $appends = ['reviews_avg_rating'];


}
