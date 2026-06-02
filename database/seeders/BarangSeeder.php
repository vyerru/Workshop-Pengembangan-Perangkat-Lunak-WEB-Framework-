<?php
namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Karburator Astrea Grand (Original)', 'harga' => 250000],
            ['nama' => 'Intake Manifold Supra X 125',        'harga' => 75000],
            ['nama' => 'Rantai Set (Drive Chain Kit)',       'harga' => 155000],
            ['nama' => 'Lampu Depan (Headlight) Grand',      'harga' => 120000],
            ['nama' => 'Shockbreaker Belakang (Double)',     'harga' => 350000],
            ['nama' => 'Kabel Gas (Throttle Cable)',         'harga' => 45000],
            ['nama' => 'Filter Udara Supra X',               'harga' => 35000],
            ['nama' => 'Busi NGK C7HSA',                     'harga' => 25000],
            ['nama' => 'Kampas Rem Depan (Discpad)',         'harga' => 55000],
            ['nama' => 'Paking Top Set Grand/Supra',         'harga' => 180000],
        ];

        foreach ($data as $item) {
            Barang::create($item + ['timestamp' => now()]);
        }
    }
}