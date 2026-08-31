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
            'barang_id'         => Barang::factory(),
            'user_id'           => User::factory()->staff(),
            'tanggal'           => fake()->date(),
            'jumlah'            => fake()->numberBetween(1, 20),
            'tujuan'            => fake()->company(),
            'status'            => 'disetujui',
            'approved_by'       => null,
            'catatan_penolakan' => null,
            'approved_at'       => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'            => 'pending',
            'approved_by'       => null,
            'catatan_penolakan' => null,
            'approved_at'       => null,
        ]);
    }

    public function ditolak(?string $alasan = 'Stok fisik tidak mencukupi'): static
    {
        return $this->state(fn (array $attributes) => [
            'status'            => 'ditolak',
            'catatan_penolakan' => $alasan,
            'approved_at'       => now(),
        ]);
    }
}
