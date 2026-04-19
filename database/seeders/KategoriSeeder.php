<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::create(['nama_kategori' => 'Elektronik']);
        Kategori::create(['nama_kategori' => 'Pakaian']);
        Kategori::create(['nama_kategori' => 'Makanan']);
        Kategori::create(['nama_kategori' => 'Peralatan Kantor']);
        Kategori::create(['nama_kategori' => 'Alat Tulis']);
    }
}
