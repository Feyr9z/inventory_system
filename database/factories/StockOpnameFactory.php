<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\StockOpname;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockOpnameFactory extends Factory
{
    protected $model = StockOpname::class;

    public function definition(): array
    {
        return [
            'barang_id'  => Barang::factory(),
            'stok_fisik' => 50,
            'selisih'    => 0,
            'tanggal'    => fake()->date(),
        ];
    }
}
