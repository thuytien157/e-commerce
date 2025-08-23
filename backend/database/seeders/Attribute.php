<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Attribute extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('attributes')->insert([
            [ //1
                'name' => 'Kích thước',
                'type' => 'select',
            ],
            [ //1
                'name' => 'Màu sắc',
                'type' => 'color',
            ],
        ]);
    }
}
