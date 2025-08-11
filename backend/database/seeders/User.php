<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'Thủy Tiên',
                'email' => 'thuytien@example.com',
                'password' => Hash::make('password123'),
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocKLx6mPcGrcQJM5Knszm9fFbKnqRO3vDtS1mKom-3_VU8XP9G8=s96-c',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
