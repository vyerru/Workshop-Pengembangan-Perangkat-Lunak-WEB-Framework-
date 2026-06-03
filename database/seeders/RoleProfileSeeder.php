<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Admin;
use App\Models\Sales;

class RoleProfileSeeder extends Seeder
{
    public function run(): void
    {
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
