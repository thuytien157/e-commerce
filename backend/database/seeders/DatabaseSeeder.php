<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            User::class,
            Category::class,
            Product::class,
            Variant::class,
            Variant_image::class,
            Attribute::class,
            Attribute_value::class,
            Variant_attribute::class
        ]);
    }
}
