<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $billboard = Kategori::where('nama_kategori', 'Bahan Billboard')->first();
        $spanduk = Kategori::where('nama_kategori', 'Spanduk & Banner')->first();
        $baliho = Kategori::where('nama_kategori', 'Baliho')->first();
        $vertical = Kategori::where('nama_kategori', 'Vertical Banner')->first();
        $event = Kategori::where('nama_kategori', 'Event Toolbox')->first();
        $struktur = Kategori::where('nama_kategori', 'Struktur & Frame')->first();
        $hardware = Kategori::where('nama_kategori', 'Hardware & Perlengkapan')->first();
        $supplies = Kategori::where('nama_kategori', 'Supplies & Material')->first();

        // === BAHAN BILLBOARD ===
        Barang::create([
            'nama_barang' => 'Printing Billboard 3x12m',
            'kategori_id' => $billboard->id,
            'stok' => 8,
            'stok_minimum' => 2,
            'lokasi' => 'Gudang Utama',
        ]);

        Barang::create([
            'nama_barang' => 'Vinyl Glossy 440gsm',
            'kategori_id' => $billboard->id,
            'stok' => 50,
            'stok_minimum' => 10,
            'lokasi' => 'Gudang Utama',
        ]);

        Barang::create([
            'nama_barang' => 'Mesh Laminating Film',
            'kategori_id' => $billboard->id,
            'stok' => 30,
            'stok_minimum' => 5,
            'lokasi' => 'Rak A1',
        ]);

        // === SPANDUK & BANNER ===
        Barang::create([
            'nama_barang' => 'Spanduk Flexi 2x10m',
            'kategori_id' => $spanduk->id,
            'stok' => 25,
            'stok_minimum' => 5,
            'lokasi' => 'Gudang Utama',
        ]);

        Barang::create([
            'nama_barang' => 'Banner Katun 2x3m',
            'kategori_id' => $spanduk->id,
            'stok' => 40,
            'stok_minimum' => 10,
            'lokasi' => 'Rak B1',
        ]);

        Barang::create([
            'nama_barang' => 'Kain Backdrop Event',
            'kategori_id' => $spanduk->id,
            'stok' => 35,
            'stok_minimum' => 8,
            'lokasi' => 'Rak B2',
        ]);

        Barang::create([
            'nama_barang' => 'Vinyl Sticker Roll',
            'kategori_id' => $spanduk->id,
            'stok' => 60,
            'stok_minimum' => 15,
            'lokasi' => 'Rak B3',
        ]);

        // === BALIHO ===
        Barang::create([
            'nama_barang' => 'Baliho Aluminium Frame 4x6m',
            'kategori_id' => $baliho->id,
            'stok' => 5,
            'stok_minimum' => 1,
            'lokasi' => 'Gudang Utama',
        ]);

        Barang::create([
            'nama_barang' => 'Printing Baliho 4x6m',
            'kategori_id' => $baliho->id,
            'stok' => 12,
            'stok_minimum' => 3,
            'lokasi' => 'Gudang Utama',
        ]);

        Barang::create([
            'nama_barang' => 'Plywood Baliho Backing',
            'kategori_id' => $baliho->id,
            'stok' => 45,
            'stok_minimum' => 10,
            'lokasi' => 'Gudang Utama',
        ]);

        // === VERTICAL BANNER ===
        Barang::create([
            'nama_barang' => 'Vertical Banner Stand 2m',
            'kategori_id' => $vertical->id,
            'stok' => 20,
            'stok_minimum' => 5,
            'lokasi' => 'Rak C1',
        ]);

        Barang::create([
            'nama_barang' => 'Printing Vertical Banner 0.6x2m',
            'kategori_id' => $vertical->id,
            'stok' => 50,
            'stok_minimum' => 10,
            'lokasi' => 'Rak C2',
        ]);

        Barang::create([
            'nama_barang' => 'Roll-up Banner Canvas',
            'kategori_id' => $vertical->id,
            'stok' => 30,
            'stok_minimum' => 8,
            'lokasi' => 'Rak C3',
        ]);

        Barang::create([
            'nama_barang' => 'X-Banner Stand Premium',
            'kategori_id' => $vertical->id,
            'stok' => 15,
            'stok_minimum' => 3,
            'lokasi' => 'Rak C4',
        ]);

        // === EVENT TOOLBOX ===
        Barang::create([
            'nama_barang' => 'Tent Event 4x4m',
            'kategori_id' => $event->id,
            'stok' => 8,
            'stok_minimum' => 2,
            'lokasi' => 'Gudang Event',
        ]);

        Barang::create([
            'nama_barang' => 'Kursi Lipat Aluminum',
            'kategori_id' => $event->id,
            'stok' => 100,
            'stok_minimum' => 20,
            'lokasi' => 'Gudang Event',
        ]);

        Barang::create([
            'nama_barang' => 'Meja Event Lipat',
            'kategori_id' => $event->id,
            'stok' => 50,
            'stok_minimum' => 10,
            'lokasi' => 'Gudang Event',
        ]);

        Barang::create([
            'nama_barang' => 'Lampu Staging LED',
            'kategori_id' => $event->id,
            'stok' => 30,
            'stok_minimum' => 5,
            'lokasi' => 'Gudang Event',
        ]);

        Barang::create([
            'nama_barang' => 'Sound System Portable',
            'kategori_id' => $event->id,
            'stok' => 12,
            'stok_minimum' => 2,
            'lokasi' => 'Gudang Event',
        ]);

        Barang::create([
            'nama_barang' => 'Projection Screen 5x3m',
            'kategori_id' => $event->id,
            'stok' => 6,
            'stok_minimum' => 1,
            'lokasi' => 'Gudang Event',
        ]);

        // === STRUKTUR & FRAME ===
        Barang::create([
            'nama_barang' => 'Profil Aluminium 40x40mm',
            'kategori_id' => $struktur->id,
            'stok' => 200,
            'stok_minimum' => 50,
            'lokasi' => 'Gudang Material',
        ]);

        Barang::create([
            'nama_barang' => 'Besi Galvanis UNP 100',
            'kategori_id' => $struktur->id,
            'stok' => 150,
            'stok_minimum' => 40,
            'lokasi' => 'Gudang Material',
        ]);

        Barang::create([
            'nama_barang' => 'Kayu Meranti 5cm x 10cm',
            'kategori_id' => $struktur->id,
            'stok' => 300,
            'stok_minimum' => 100,
            'lokasi' => 'Gudang Material',
        ]);

        Barang::create([
            'nama_barang' => 'PVC Pipe 4 Inch',
            'kategori_id' => $struktur->id,
            'stok' => 80,
            'stok_minimum' => 20,
            'lokasi' => 'Rak D1',
        ]);

        // === HARDWARE & PERLENGKAPAN ===
        Barang::create([
            'nama_barang' => 'Baut & Mur Set',
            'kategori_id' => $hardware->id,
            'stok' => 500,
            'stok_minimum' => 100,
            'lokasi' => 'Rak Hardware 1',
        ]);

        Barang::create([
            'nama_barang' => 'Double Tape 50mm x 50m',
            'kategori_id' => $hardware->id,
            'stok' => 200,
            'stok_minimum' => 30,
            'lokasi' => 'Rak Hardware 2',
        ]);

        Barang::create([
            'nama_barang' => 'Kawat Stainless 2mm',
            'kategori_id' => $hardware->id,
            'stok' => 100,
            'stok_minimum' => 20,
            'lokasi' => 'Rak Hardware 3',
        ]);

        Barang::create([
            'nama_barang' => 'Chain Hook & Hanging System',
            'kategori_id' => $hardware->id,
            'stok' => 150,
            'stok_minimum' => 30,
            'lokasi' => 'Rak Hardware 4',
        ]);

        Barang::create([
            'nama_barang' => 'Velcro Industrial Strength',
            'kategori_id' => $hardware->id,
            'stok' => 250,
            'stok_minimum' => 50,
            'lokasi' => 'Rak Hardware 5',
        ]);

        // === SUPPLIES & MATERIAL ===
        Barang::create([
            'nama_barang' => 'Cat Primer White 5L',
            'kategori_id' => $supplies->id,
            'stok' => 40,
            'stok_minimum' => 8,
            'lokasi' => 'Gudang Chemical',
        ]);

        Barang::create([
            'nama_barang' => 'Solvent Ink Cyan 1L',
            'kategori_id' => $supplies->id,
            'stok' => 20,
            'stok_minimum' => 5,
            'lokasi' => 'Gudang Chemical',
        ]);

        Barang::create([
            'nama_barang' => 'Adhesive Foam 2mm',
            'kategori_id' => $supplies->id,
            'stok' => 80,
            'stok_minimum' => 15,
            'lokasi' => 'Rak Supply 1',
        ]);

        Barang::create([
            'nama_barang' => 'Laminating Pouch A4',
            'kategori_id' => $supplies->id,
            'stok' => 500,
            'stok_minimum' => 100,
            'lokasi' => 'Rak Supply 2',
        ]);

        Barang::create([
            'nama_barang' => 'Tissue Putih Roll 100m',
            'kategori_id' => $supplies->id,
            'stok' => 60,
            'stok_minimum' => 10,
            'lokasi' => 'Rak Supply 3',
        ]);

        Barang::create([
            'nama_barang' => 'Masking Tape 24mm x 50m',
            'kategori_id' => $supplies->id,
            'stok' => 120,
            'stok_minimum' => 20,
            'lokasi' => 'Rak Supply 4',
        ]);

        Barang::create([
            'nama_barang' => 'Bubble Wrap Roll 50cm',
            'kategori_id' => $supplies->id,
            'stok' => 100,
            'stok_minimum' => 20,
            'lokasi' => 'Rak Supply 5',
        ]);
    }
}
