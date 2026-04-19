<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::create(['nama_kategori' => 'Bahan Billboard']);
        Kategori::create(['nama_kategori' => 'Spanduk & Banner']);
        Kategori::create(['nama_kategori' => 'Baliho']);
        Kategori::create(['nama_kategori' => 'Vertical Banner']);
        Kategori::create(['nama_kategori' => 'Event Toolbox']);
        Kategori::create(['nama_kategori' => 'Struktur & Frame']);
        Kategori::create(['nama_kategori' => 'Hardware & Perlengkapan']);
        Kategori::create(['nama_kategori' => 'Supplies & Material']);
    }
}
