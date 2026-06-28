<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Admin;
use App\Models\Sales;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $vendorUser = User::firstOrCreate(
            ['email' => 'vendor@kantin.com'],
            ['name' => 'Kantin Mak Murah', 'password' => Hash::make('password'), 'role' => User::ROLE_VENDOR]
        );
        if (!$vendorUser->vendor) {
            Vendor::create(['user_id' => $vendorUser->id, 'nama_vendor' => $vendorUser->name]);
        }

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@student.com'],
            ['name' => 'Vier Mahasiswa', 'password' => Hash::make('password'), 'role' => User::ROLE_CUSTOMER]
        );
        if (!$customerUser->customer) {
            Customer::create(['user_id' => $customerUser->id, 'nama' => $customerUser->name]);
        }

        User::firstOrCreate(
            ['email' => 'admin@geo.com'],
            ['name' => 'Admin Geolocation', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );

        User::firstOrCreate(
            ['email' => 'sales@geo.com'],
            ['name' => 'Sales Lapangan', 'password' => Hash::make('password'), 'role' => User::ROLE_SALES]
        );

        User::firstOrCreate(
            ['email' => 'mahasiswa@student.com'],
            ['name' => 'Mahasiswa Absensi', 'password' => Hash::make('password'), 'role' => User::ROLE_MAHASISWA]
        );

        User::firstOrCreate(
            ['email' => 'admin@absen.com'],
            ['name' => 'Admin Absensi', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );
    }
}
