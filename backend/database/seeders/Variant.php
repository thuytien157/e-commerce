<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Variant extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('variants')->insert([
            [ //1
                'product_id' => 1,
                'sku' => 'SP-Q01-01-01',
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-black-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107765/products/lhautibbggb2gcypvvnm.jpg',
            ],
            [ //1
                'product_id' => 1,
                'sku' => 'SP-Q01-02-01',
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-white-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107764/products/zbsgbqhj3tds0wpkneak.jpg',
            ],
            [ //1
                'product_id' => 1,
                'sku' => 'SP-Q01-03-01',
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-black-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107765/products/lhautibbggb2gcypvvnm.jpg',
            ],
            [ //1
                'product_id' => 1,
                'sku' => 'SP-Q01-04-01',
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-white-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107764/products/zbsgbqhj3tds0wpkneak.jpg',
            ],
            [ //1
                'product_id' => 2,
                'sku' => 'SP-Q01-05-01',
                'stock_quantity' => 100,
                'slug' => 'ao-unisex-varsity-sportive-black-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107772/products/ctqv9hw7tf72jqmz1qcs.webp',
            ],
            [ //1
                'product_id' => 2,
                'sku' => 'SP-Q01-06-01',
                'stock_quantity' => 100,
                'slug' => 'ao-unisex-varsity-sportive-black-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107774/products/spkhx7zju8whijrdckoz.webp',
            ],
            [ //1
                'product_id' => 2,
                'sku' => 'SP-Q01-07-01',
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-black-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107772/products/ctqv9hw7tf72jqmz1qcs.webp',
            ],
            [ //1
                'product_id' => 2,
                'sku' => 'SP-Q01-08-01',
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-white-s',
                'main_image_url' => 'https://res.cloudinary.com/daqhc6id1/image/upload/v1741107774/products/spkhx7zju8whijrdckoz.webp',
            ],
        ]);
    }
}
