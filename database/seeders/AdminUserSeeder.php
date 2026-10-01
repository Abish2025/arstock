<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;         
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Administrador principal
        User::updateOrCreate(
            ['email' => 'admin@arstock.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('arstock123'),
                'rol'      => 'admin',
                'activo'   => true,
                'telefono' => '1134567890',
            ]
        );

        // Cajero / Empleado de prueba
        User::updateOrCreate(
            ['email' => 'cajero@arstock.com'],
            [
                'name'     => 'Cajero de Turno',
                'password' => Hash::make('arstock123'),
                'rol'      => 'cajero',
                'activo'   => true,
                'telefono' => '1198765432',
            ]
        );
    }
}