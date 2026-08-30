<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangMasukFactory extends Factory
{
    protected $model = BarangMasuk::class;

    public function definition(): array
    {
        $jumlah = fake()->numberBetween(10, 100);

        return [
            'barang_id'   => Barang::factory(),
            'user_id'     => User::factory()->staff(),
            'tanggal'     => fake()->date(),
            'jumlah'      => $jumlah,
            'sisa_jumlah' => $jumlah,
            'sumber'      => fake()->company(),
        ];
    }
}
