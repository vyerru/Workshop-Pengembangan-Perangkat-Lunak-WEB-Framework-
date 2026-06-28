<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Admin;
use App\Models\Sales;

class RoleProfileSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $roleMap = [];
        foreach (['admin', 'vendor', 'customer', 'sales', 'mahasiswa'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $roleMap[$roleName] = $role->id;
            }
        }

        User::whereNull('role_id')->chunk(100, function ($users) use ($roleMap) {
            foreach ($users as $user) {
                if ($user->role && isset($roleMap[$user->role])) {
                    $user->update(['role_id' => $roleMap[$user->role]]);
                }
            }
        });

        $users = User::all();

        foreach ($users as $user) {
            switch ($user->role) {
                case User::ROLE_VENDOR:
                    if (!$user->vendor) {
                        Vendor::create([
                            'user_id' => $user->id,
                            'nama_vendor' => $user->name,
                        ]);
                        $this->command->info("Created Vendor profile for {$user->name}");
                    }
                    break;

                case User::ROLE_CUSTOMER:
                    if (!$user->customer) {
                        Customer::create([
                            'user_id' => $user->id,
                            'nama' => $user->name,
                            'alamat' => '',
                        ]);
                        $this->command->info("Created Customer profile for {$user->name}");
                    }
                    break;

                case User::ROLE_ADMIN:
                    if (!$user->admin) {
                        Admin::create([
                            'user_id' => $user->id,
                            'nama' => $user->name,
                        ]);
                        $this->command->info("Created Admin profile for {$user->name}");
                    }
                    break;

                case User::ROLE_SALES:
                    if (!$user->sales) {
                        Sales::create([
                            'user_id' => $user->id,
                            'nama' => $user->name,
                        ]);
                        $this->command->info("Created Sales profile for {$user->name}");
                    }
                    break;
            }
        }
    }
}
