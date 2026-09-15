<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;         
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Evita crear duplicados si ya existe
        User::updateOrCreate(
            ['email' => 'admin@arstock.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('arstock123'),
            ]
        );
    }
}