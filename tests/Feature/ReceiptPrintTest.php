<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptPrintTest extends TestCase
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

        $kategori = Kategori::create(['nama_kategori' => 'Advertising Supplies']);
        $this->barang = Barang::create([
            'nama_barang'  => 'Sticker Vinyl Glossy 100m',
            'kategori_id'  => $kategori->id,
            'stok'         => 50,
            'stok_minimum' => 10,
            'lokasi'       => 'Gudang Utama - Rak B2',
        ]);
    }

    public function test_print_masuk_receipt_renders_successfully(): void
    {
        $masuk = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'jumlah'      => 50,
            'sisa_jumlah' => 50,
            'tanggal'     => now()->format('Y-m-d'),
            'sumber'      => 'PT Sumber Grafika Sentosa',
            'user_id'     => $this->staff->id,
        ]);

        $response = $this->actingAs($this->staff)->get(route('inventory.receipt.masuk', $masuk->id));
        $response->assertStatus(200);
        $response->assertSee('PT ATHA ANAKHATULISTIWA');
        $response->assertSee('Surat Bukti Penerimaan Barang');
        $response->assertSee('PT Sumber Grafika Sentosa');
        $response->assertSee('IN-' . now()->format('Ymd'));
    }

    public function test_print_keluar_receipt_renders_with_fifo_breakdown(): void
    {
        $lot = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'jumlah'      => 50,
            'sisa_jumlah' => 30,
            'tanggal'     => now()->format('Y-m-d'),
            'sumber'      => 'PT Vendor Asal',
            'user_id'     => $this->staff->id,
        ]);

        $keluar = BarangKeluar::create([
            'barang_id' => $this->barang->id,
            'jumlah'    => 20,
            'tanggal'   => now()->format('Y-m-d'),
            'tujuan'    => 'Proyek Billboard Thamrin',
            'user_id'   => $this->staff->id,
        ]);

        BarangKeluarDetail::create([
            'barang_keluar_id' => $keluar->id,
            'barang_masuk_id'  => $lot->id,
            'jumlah_diambil'   => 20,
        ]);

        $response = $this->actingAs($this->staff)->get(route('inventory.receipt.keluar', $keluar->id));
        $response->assertStatus(200);
        $response->assertSee('PT ATHA ANAKHATULISTIWA');
        $response->assertSee('Surat Jalan & Bukti Pengeluaran Barang', false);
        $response->assertSee('Proyek Billboard Thamrin');
        $response->assertSee('Lot #' . $lot->id);
        $response->assertSee('20 Unit');
    }

    public function test_print_opname_receipt_renders_successfully(): void
    {
        $opname = StockOpname::create([
            'barang_id'  => $this->barang->id,
            'stok_fisik' => 55,
            'selisih'    => 5,
            'tanggal'    => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('inventory.receipt.opname', $opname->id));
        $response->assertStatus(200);
        $response->assertSee('PT ATHA ANAKHATULISTIWA');
        $response->assertSee('Berita Acara Rekonsiliasi & Stock Opname Fisik', false);
        $response->assertSee('Surplus Fisik (+5)');
        $response->assertSee('OPN-' . now()->format('Ymd'));
    }
}
