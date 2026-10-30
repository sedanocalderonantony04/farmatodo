<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@farmatodo.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '01-6104000',
            'address' => 'Av. Principal 123, Lima',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Farmacéutico Demo',
            'email' => 'farmaceutico@farmatodo.test',
            'password' => Hash::make('farm1234'),
            'role' => 'farmaceutico',
            'phone' => '01-6104001',
            'address' => 'Av. Secundaria 456, Lima',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Cajero Demo',
            'email' => 'cajero@farmatodo.test',
            'password' => Hash::make('cajero12'),
            'role' => 'cajero',
            'phone' => '01-6104002',
            'address' => 'Jr. Tercera 789, Lima',
            'is_active' => true,
        ]);
    }
}