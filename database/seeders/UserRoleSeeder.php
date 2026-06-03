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
        $vendorUser = User::create([
            'name' => 'Kantin Mak Murah',
            'email' => 'vendor@kantin.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VENDOR,
        ]);
        Vendor::create(['user_id' => $vendorUser->id, 'nama_vendor' => $vendorUser->name]);

        $customerUser = User::create([
            'name' => 'Vier Mahasiswa',
            'email' => 'customer@student.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
        ]);
        Customer::create(['user_id' => $customerUser->id, 'nama' => $customerUser->name]);

        $adminUser = User::create([
            'name' => 'Admin Geolocation',
            'email' => 'admin@geo.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
        Admin::create(['user_id' => $adminUser->id, 'nama' => $adminUser->name]);

        $salesUser = User::create([
            'name' => 'Sales Lapangan',
            'email' => 'sales@geo.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SALES,
        ]);
        Sales::create(['user_id' => $salesUser->id, 'nama' => $salesUser->name]);
    }
}
