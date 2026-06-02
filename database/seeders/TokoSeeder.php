<?php

namespace Database\Seeders;

use App\Models\Toko;
use App\Models\User;
use Illuminate\Database\Seeder;

class TokoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        if (!$admin) {
            $admin = User::factory()->create([
                'name'  => 'Admin Toko',
                'email' => 'admin@toko.com',
                'role'  => User::ROLE_ADMIN,
            ]);
        }

        Toko::factory()->count(5)->create([
            'created_by' => $admin->id,
        ]);

        $this->command->info('5 toko berhasil dibuat.');
    }
}
