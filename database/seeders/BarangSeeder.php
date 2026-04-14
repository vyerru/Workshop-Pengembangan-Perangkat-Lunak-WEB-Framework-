<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Configuration\Php;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barang')->insert([
            [
                'nama'      => 'Karburator Astrea Grand (Original)',
                'harga'     => 250000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Intake Manifold Supra X 125',
                'harga'     => 75000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Rantai Set (Drive Chain Kit)',
                'harga'     => 155000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Lampu Depan (Headlight) Grand',
                'harga'     => 120000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Shockbreaker Belakang (Double)',
                'harga'     => 350000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Kabel Gas (Throttle Cable)',
                'harga'     => 45000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Filter Udara Supra X',
                'harga'     => 35000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Busi NGK C7HSA',
                'harga'     => 25000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Kampas Rem Depan (Discpad)',
                'harga'     => 55000,
                'timestamp' => now(),
            ],
            [
                'nama'      => 'Paking Top Set Grand/Supra',
                'harga'     => 180000,
                'timestamp' => now(),
            ],
        ]);
    }
}