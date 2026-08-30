<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangKeluarFactory extends Factory
{
    protected $model = BarangKeluar::class;

    public function definition(): array
    {
        return [
            'barang_id' => Barang::factory(),
            'user_id'   => User::factory()->staff(),
            'tanggal'   => fake()->date(),
            'jumlah'    => fake()->numberBetween(1, 20),
            'tujuan'    => fake()->company(),
        ];
    }
}
