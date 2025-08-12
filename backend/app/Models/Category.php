<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    public $timestamps = false;
    protected $fillable = [
        'parent_id',
        'name',
        'slug'
    ];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getAllChildIds()
    {
        // tạo mảng id
        $ids = [];

        // duyệt children danh mục đc gọi
        foreach ($this->children as $key => $child) {
            // thêm id của danh mục con của danh mục đc gọi vào mảng ids luôn
            $ids[] = $child->id;
            //gộp mảng id vừa làm xong và gọi danh mục con tiếp tục của danh mục được gọi trc đó
            $ids = array_merge($ids, $child->getAllChildIds());
        }

        // trả về mảng ids
        return $ids;
    }

    protected $appends = ['all_products_count'];

    public function getAllProductsCountAttribute()
    {
        $count = $this->products()->count();

        foreach ($this->children as $child) {
            $count += $child->products()->count();
        }

        return $count;
    }
}
