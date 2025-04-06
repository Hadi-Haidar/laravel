<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create manager admin
        Admin::create([
            'name' => 'Manager Admin',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'manager'
        ]);

        // Create seller admin
        Admin::create([
            'name' => 'Seller Admin',
            'email' => 'seller@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'seller'
        ]);
    }
}