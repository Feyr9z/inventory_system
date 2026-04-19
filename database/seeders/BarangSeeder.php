<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $elektronik = Kategori::where('nama_kategori', 'Elektronik')->first();
        $pakaian = Kategori::where('nama_kategori', 'Pakaian')->first();
        $makanan = Kategori::where('nama_kategori', 'Makanan')->first();

        Barang::create([
            'nama_barang' => 'Laptop Dell',
            'kategori_id' => $elektronik->id,
            'stok' => 5,
            'stok_minimum' => 2,
            'lokasi' => 'Gudang A',
        ]);

        Barang::create([
            'nama_barang' => 'Mouse Wireless',
            'kategori_id' => $elektronik->id,
            'stok' => 20,
            'stok_minimum' => 5,
            'lokasi' => 'Rak 1',
        ]);

        Barang::create([
            'nama_barang' => 'Kaos Putih',
            'kategori_id' => $pakaian->id,
            'stok' => 50,
            'stok_minimum' => 10,
            'lokasi' => 'Gudang B',
        ]);

        Barang::create([
            'nama_barang' => 'Celana Jeans',
            'kategori_id' => $pakaian->id,
            'stok' => 30,
            'stok_minimum' => 8,
            'lokasi' => 'Gudang B',
        ]);

        Barang::create([
            'nama_barang' => 'Beras 10kg',
            'kategori_id' => $makanan->id,
            'stok' => 100,
            'stok_minimum' => 20,
            'lokasi' => 'Gudang C',
        ]);
    }
}
