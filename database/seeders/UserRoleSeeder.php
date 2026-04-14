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
        // Buat Akun Vendor
        User::create([
            'name' => 'Kantin Mak Murah',
            'email' => 'vendor@kantin.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VENDOR,
        ]);

        // Buat Akun Customer
        User::create([
            'name' => 'Vier Mahasiswa',
            'email' => 'customer@student.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
        ]);
    }
}
