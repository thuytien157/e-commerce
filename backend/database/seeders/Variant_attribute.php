<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Variant_attribute extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('variant_attributes')->insert([
            // Variant 1: Quần đen size S
            ['variant_id' => 1, 'attribute_value_id' => 1], // size S
            ['variant_id' => 1, 'attribute_value_id' => 3], // màu #111111

            // Variant 2: Quần trắng size S
            ['variant_id' => 2, 'attribute_value_id' => 1], // size S
            ['variant_id' => 2, 'attribute_value_id' => 5],

            // Variant 3: Quần đen size M
            ['variant_id' => 3, 'attribute_value_id' => 2], // size M
            ['variant_id' => 3, 'attribute_value_id' => 3], // màu #111111

            // Variant 4: Quần trắng size M
            ['variant_id' => 4, 'attribute_value_id' => 2], // size M
            ['variant_id' => 4, 'attribute_value_id' => 5],

            // Variant 5: Áo đen size S
            ['variant_id' => 5, 'attribute_value_id' => 1], // size S
            ['variant_id' => 5, 'attribute_value_id' => 3], // màu #111111

            // Variant 6: Áo đỏ size S
            ['variant_id' => 6, 'attribute_value_id' => 1], // size S
            ['variant_id' => 6, 'attribute_value_id' => 4], // màu #fd0100

            // Variant 7: Áo đen size M
            ['variant_id' => 7, 'attribute_value_id' => 2], // size M
            ['variant_id' => 7, 'attribute_value_id' => 3], // màu #111111

            // Variant 8: Áo đỏ size M
            ['variant_id' => 8, 'attribute_value_id' => 2], // size M
            ['variant_id' => 8, 'attribute_value_id' => 4], // màu #fd0100
        ]);
    }
}
