<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $this->createVendorMenus('Kantin Mak Murah', 'vendor@kantin.com', [
            ['nama_menu' => 'Nasi Goreng', 'harga' => 15000],
            ['nama_menu' => 'Mie Ayam', 'harga' => 12000],
            ['nama_menu' => 'Bakso', 'harga' => 10000],
            ['nama_menu' => 'Es Teh', 'harga' => 5000],
            ['nama_menu' => 'Ayam Geprek', 'harga' => 18000],
        ]);

        $this->createVendorMenus('Nasi Goreng 77', 'vendor2@kantin.com', [
            ['nama_menu' => 'Nasi Goreng Spesial', 'harga' => 20000],
            ['nama_menu' => 'Nasi Goreng Biasa', 'harga' => 12000],
            ['nama_menu' => 'Nasi Goreng Seafood', 'harga' => 25000],
            ['nama_menu' => 'Telur Ceplok', 'harga' => 4000],
            ['nama_menu' => 'Kerupuk', 'harga' => 2000],
        ]);

        $this->createVendorMenus('Nasi Pecel Bu Ani', 'vendor3@kantin.com', [
            ['nama_menu' => 'Nasi Pecel Biasa', 'harga' => 10000],
            ['nama_menu' => 'Nasi Pecel + Ayam', 'harga' => 18000],
            ['nama_menu' => 'Nasi Pecel + Empal', 'harga' => 20000],
            ['nama_menu' => 'Pecel Telur', 'harga' => 8000],
            ['nama_menu' => 'Es Jeruk', 'harga' => 4000],
            ['nama_menu' => 'Nasi Putih', 'harga' => 4000],
        ]);
    }

    private function createVendorMenus(string $namaVendor, string $email, array $items): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $namaVendor,
                'password' => Hash::make('password'),
                'role' => User::ROLE_VENDOR,
            ]
        );

        $vendor = Vendor::firstOrCreate(
            ['user_id' => $user->id],
            ['nama_vendor' => $namaVendor]
        );

        foreach ($items as $item) {
            Menu::firstOrCreate(
                ['vendor_id' => $vendor->id, 'nama_menu' => $item['nama_menu']],
                ['harga' => $item['harga'], 'path_gambar' => null]
            );
        }

        $this->command->info("Created {$namaVendor} with " . count($items) . " menus.");
    }
}
