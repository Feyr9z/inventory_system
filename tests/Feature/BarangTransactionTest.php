<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $admin;
    protected Barang $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->staff()->create();
        $this->admin = User::factory()->admin()->create();

        $kategori = Kategori::create(['nama_kategori' => 'Advertising Media']);
        $this->barang = Barang::create([
            'nama_barang'  => 'Roll Banner Stand 2x0.8m',
            'kategori_id'  => $kategori->id,
            'stok'         => 0,
            'stok_minimum' => 5,
            'lokasi'       => 'Rak Banner 1',
        ]);
    }

    /**
     * Test input barang masuk via HTTP endpoint:
     * - Memperbarui stok agregat
     * - Menginisialisasi sisa_jumlah = jumlah
     * - Menyimpan user_id penginput
     */
    public function test_barang_masuk_stores_lot_with_sisa_jumlah_and_user_id(): void
    {
        $response = $this->actingAs($this->staff)->post(route('inventory.transaksi.masuk.store'), [
            'barang_id' => $this->barang->id,
            'jumlah'    => 25,
            'tanggal'   => '2026-08-15',
            'sumber'    => 'PT Vendor Cetak',
        ]);

        $response->assertRedirect(route('inventory.transaksi.masuk.create'));
        $response->assertSessionHas('success');

        $this->barang->refresh();
        $this->assertEquals(25, $this->barang->stok);

        $lot = BarangMasuk::first();
        $this->assertNotNull($lot);
        $this->assertEquals(25, $lot->jumlah);
        $this->assertEquals(25, $lot->sisa_jumlah);
        $this->assertEquals($this->staff->id, $lot->user_id);
    }

    /**
     * Test input barang keluar via HTTP endpoint:
     * - Staff submit -> status pending (stok dan lot belum terpotong)
     * - Kepala Gudang menyetujui -> alokasi FIFO dieksekusi & stok terpotong
     */
    public function test_barang_keluar_allocates_fifo_via_http_post(): void
    {
        // Masukkan 2 lot
        $lot1 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'sumber'      => 'Supplier 1',
        ]);
        $lot2 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-05',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Supplier 2',
        ]);
        $this->barang->update(['stok' => 30]);

        // 1. Staff kirim request barang keluar 15 unit (masuk antrean pending)
        $response = $this->actingAs($this->staff)->post(route('inventory.transaksi.keluar.store'), [
            'barang_id' => $this->barang->id,
            'jumlah'    => 15,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Event Pameran Expo',
        ]);

        $response->assertRedirect(route('inventory.transaksi.keluar.create'));
        $response->assertSessionHas('success');

        // Pastikan status pending dan stok belum terpotong
        $pengajuan = \App\Models\BarangKeluar::where('tujuan', 'Event Pameran Expo')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals('pending', $pengajuan->status);

        $lot1->refresh();
        $lot2->refresh();
        $this->barang->refresh();
        $this->assertEquals(10, $lot1->sisa_jumlah);
        $this->assertEquals(20, $lot2->sisa_jumlah);
        $this->assertEquals(30, $this->barang->stok);

        // 2. Kepala Gudang menyetujui pengajuan
        $kepalaGudang = User::factory()->kepalaGudang()->create();
        $approveResponse = $this->actingAs($kepalaGudang)->post(route('inventory.transaksi.approval.approve', $pengajuan->id));

        $approveResponse->assertRedirect(route('inventory.transaksi.approval.index', ['tab' => 'pending']));
        $approveResponse->assertSessionHas('success');

        // Verifikasi alokasi FIFO (10 dari lot1, 5 dari lot2)
        $lot1->refresh();
        $lot2->refresh();
        $this->barang->refresh();

        $this->assertEquals(0, $lot1->sisa_jumlah);
        $this->assertEquals(15, $lot2->sisa_jumlah);
        $this->assertEquals(15, $this->barang->stok);
    }

    /**
     * Test stock opname via HTTP endpoint:
     * - Menyesuaikan stok agregat
     * - Menjaga paritas lot FIFO
     */
    public function test_stock_opname_adjusts_stok_and_lots_via_http_post(): void
    {
        BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->admin->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Supplier Awal',
        ]);
        $this->barang->update(['stok' => 20]);

        // Lakukan opname fisik = 25 (+5 surplus)
        $response = $this->actingAs($this->admin)->post(route('inventory.transaksi.opname.store'), [
            'barang_id'  => $this->barang->id,
            'stok_fisik' => 25,
            'tanggal'    => '2026-08-30',
        ]);

        $response->assertRedirect(route('inventory.transaksi.opname.create'));
        $response->assertSessionHas('success');

        $this->barang->refresh();
        $this->assertEquals(25, $this->barang->stok);

        $totalSisaLots = BarangMasuk::where('barang_id', $this->barang->id)->sum('sisa_jumlah');
        $this->assertEquals(25, $totalSisaLots);
    }
}
