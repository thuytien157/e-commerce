<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Category extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [ //1
                'parent_id' => null,
                'name' => 'Quần áo',
                'slug' => 'quan-ao',
            ],
            [ //2
                'parent_id' => 1,
                'name' => 'Quần',
                'slug' => 'quan-ao/quan',
            ]
        ]);
    }
}
