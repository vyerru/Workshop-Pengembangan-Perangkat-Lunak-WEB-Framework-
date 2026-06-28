<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator'],
            ['name' => 'vendor', 'display_name' => 'Vendor'],
            ['name' => 'customer', 'display_name' => 'Customer'],
            ['name' => 'sales', 'display_name' => 'Sales'],
            ['name' => 'mahasiswa', 'display_name' => 'Mahasiswa'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
