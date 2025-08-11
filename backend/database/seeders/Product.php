<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Product extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [ //1
                'category_id' => 2,
                'name' => 'Quần dài unisex ống rộng lưng thun Varsity Sportive',
                'description' => 'Thiết kế phom quần năng động, cá tính
                                Dây rút điều chỉnh giúp tùy chỉnh độ ôm vừa vặn của quần
                                Logo được thêu tinh tế ở mặt trước và túi sau, tạo điểm nhấn thời trang.
                                Chất vải cao cấp, thoải mái
                                Màu sắc hiện đại, dễ dàng phối với nhiều trang phục khác nhau',
            ],
            [ //2
                'category_id' => 2,
                'name' => 'Quần jogger unisex lưng thun Jacquard Monogram',
                'description' => 'Thiết kế phom quần năng động, cá tính
                                Dây rút điều chỉnh giúp tùy chỉnh độ ôm vừa vặn của quần
                                Logo được thêu tinh tế ở mặt trước và túi sau, tạo điểm nhấn thời trang.
                                Chất vải cao cấp, thoải mái
                                Màu sắc hiện đại, dễ dàng phối với nhiều trang phục khác nhau',
            ],

        ]);
    }
}
