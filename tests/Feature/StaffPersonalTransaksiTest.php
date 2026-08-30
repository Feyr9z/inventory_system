<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPersonalTransaksiTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_personal_transactions_page(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('inventory.transaksi.saya'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Transaksi Saya');
    }

    public function test_staff_only_sees_their_own_transactions(): void
    {
        $staffA = User::factory()->create(['name' => 'Staff Alice', 'role' => 'staff']);
        $staffB = User::factory()->create(['name' => 'Staff Bob', 'role' => 'staff']);

        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $barang = Barang::create([
            'nama_barang'  => 'Laptop Lenovo',
            'kategori_id'  => $kategori->id,
            'stok'         => 10,
            'stok_minimum' => 2,
            'lokasi'       => 'Rak A1',
        ]);

        // Transaction by Staff Alice
        BarangMasuk::create([
            'barang_id'   => $barang->id,
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'tanggal'     => now()->format('Y-m-d'),
            'sumber'      => 'PT Vendor Alice',
            'user_id'     => $staffA->id,
        ]);

        // Transaction by Staff Bob
        BarangMasuk::create([
            'barang_id'   => $barang->id,
            'jumlah'      => 5,
            'sisa_jumlah' => 5,
            'tanggal'     => now()->format('Y-m-d'),
            'sumber'      => 'PT Vendor Bob',
            'user_id'     => $staffB->id,
        ]);

        // When Staff Alice accesses personal transactions
        $responseA = $this->actingAs($staffA)->get(route('inventory.transaksi.saya'));
        $responseA->assertStatus(200);
        $responseA->assertSee('PT Vendor Alice');
        $responseA->assertDontSee('PT Vendor Bob');

        // When Staff Bob accesses personal transactions
        $responseB = $this->actingAs($staffB)->get(route('inventory.transaksi.saya'));
        $responseB->assertStatus(200);
        $responseB->assertSee('PT Vendor Bob');
        $responseB->assertDontSee('PT Vendor Alice');
    }
}
