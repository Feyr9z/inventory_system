<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition(): array
    {
        return [
            'nama_barang'  => fake()->words(3, true),
            'stok'         => 0,
            'stok_minimum' => 5,
            'lokasi'       => 'Gudang Utama',
            'kategori_id'  => Kategori::factory(),
        ];
    }
}
