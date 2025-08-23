<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Attribute_value extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('attribute_values')->insert([
            [ //1
                'attribute_id' => 1,
                'value_name' => 'S',
            ],
            [ //1
                'attribute_id' => 1,
                'value_name' => 'M',
            ],
            [ //1
                'attribute_id' => 2,
                'value_name' => '#111111',
            ],
            [ //1
                'attribute_id' => 2,
                'value_name' => '#fd0100',
            ],
            [ //1
                'attribute_id' => 2,
                'value_name' => '#ffff',
            ],

        ]);
    }
}
