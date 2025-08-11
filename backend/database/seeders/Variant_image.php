<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Variant_image extends Seeder
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
                'price' => 3990000,
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-black-s',
                'main_image_url' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive',
            ],
            [ //1
                'product_id' => 1,
                'sku' => 'SP-Q01-02-01',
                'price' => 4990000,
                'stock_quantity' => 100,
                'slug' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive-white-s',
                'main_image_url' => 'quan-dai-unisex-ong-rong-lung-thun-varsity-sportive',
            ],
        ]);
    }
}
