<?php

namespace Database\Factories;

use App\Models\Toko;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TokoFactory extends Factory
{
    protected $model = Toko::class;

    public function definition(): array
    {
        return [
            'nama_toko'     => fake()->company() . ' ' . fake()->city(),
            'alamat'        => fake()->address(),
            'latitude'      => fake()->latitude(-8, -5),
            'longitude'     => fake()->longitude(105, 115),
            'accuracy'      => fake()->randomFloat(1, 3, 20),
            'barcode_token' => Str::random(32),
            'created_by'    => User::factory(),
        ];
    }

    public function tanpaKoordinat(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude'  => null,
            'longitude' => null,
            'accuracy'  => null,
        ]);
    }
}
