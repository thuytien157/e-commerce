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
                'email' => 'thuytien.hoctap@gmail.com',
                'password' => Hash::make('Thuytien965002@'),
                'status' => 'active',
                'role' => 'customer',
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocKLx6mPcGrcQJM5Knszm9fFbKnqRO3vDtS1mKom-3_VU8XP9G8=s96-c',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Thủy Tiên1',
                'email' => 'ntttien.work@gmail.com',
                'password' => Hash::make('Thuytien965002@'),
                'status' => 'active',
                'role' => 'manager',
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocKLx6mPcGrcQJM5Knszm9fFbKnqRO3vDtS1mKom-3_VU8XP9G8=s96-c',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
