<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kantin Mak Murah',
            'email' => 'vendor@kantin.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VENDOR,
        ]);

        User::create([
            'name' => 'Vier Mahasiswa',
            'email' => 'customer@student.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
        ]);

        User::create([
            'name' => 'Admin Geolocation',
            'email' => 'admin@geo.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'name' => 'Sales Lapangan',
            'email' => 'sales@geo.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SALES,
        ]);
    }
}
