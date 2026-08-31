<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalWorkflowFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kepalaGudang;
    protected User $staff;
    protected Barang $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin        = User::factory()->admin()->create();
        $this->kepalaGudang = User::factory()->kepalaGudang()->create();
        $this->staff        = User::factory()->staff()->create();

        $kategori = Kategori::create(['nama_kategori' => 'Bahan Percetakan']);
        $this->barang = Barang::create([
            'nama_barang'  => 'Vinyl Glossy 100gr',
            'kategori_id'  => $kategori->id,
            'stok'         => 0,
            'stok_minimum' => 5,
            'lokasi'       => 'Gudang A - Rak 2',
        ]);
    }

    /**
     * Skenario 7: Barang Keluar (Input jumlah <= stok)
     * Transaksi dapat diproses dan masuk ke tahap pemeriksaan Kepala Gudang (status: pending).
     * Stok dan lot persediaan belum terpotong pada tahap ini.
     */
    public function test_skenario_7_staff_input_barang_keluar_masuk_antrean_pemeriksaan(): void
    {
        // Masukkan persediaan awal 50 unit
        $lot = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 50,
            'sisa_jumlah' => 50,
            'sumber'      => 'Supplier Grafika Utama',
        ]);
        $this->barang->update(['stok' => 50]);

        // Staff mengajukan 20 unit keluar (20 <= 50)
        $response = $this->actingAs($this->staff)->post(route('inventory.transaksi.keluar.store'), [
            'barang_id' => $this->barang->id,
            'jumlah'    => 20,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Divisi Finishing Percetakan',
        ]);

        $response->assertRedirect(route('inventory.transaksi.keluar.create'));
        $response->assertSessionHas('success');

        // Pastikan transaksi tersimpan dengan status 'pending'
        $this->assertDatabaseHas('barang_keluar', [
            'barang_id' => $this->barang->id,
            'user_id'   => $this->staff->id,
            'jumlah'    => 20,
            'status'    => 'pending',
        ]);

        // Pastikan stok dan lot belum terpotong
        $this->barang->refresh();
        $lot->refresh();
        $this->assertEquals(50, $this->barang->stok);
        $this->assertEquals(50, $lot->sisa_jumlah);
    }

    /**
     * Skenario 8: Barang Keluar (Input jumlah > stok)
     * Muncul pesan error bahwa stok tidak mencukupi dan transaksi tidak disimpan.
     */
    public function test_skenario_8_input_barang_keluar_melebihi_stok_ditolak_dengan_pesan_error(): void
    {
        // Stok barang saat ini 10 unit
        BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'sumber'      => 'Supplier B',
        ]);
        $this->barang->update(['stok' => 10]);

        // Staff mengajukan 25 unit keluar (25 > 10)
        $response = $this->actingAs($this->staff)->post(route('inventory.transaksi.keluar.store'), [
            'barang_id' => $this->barang->id,
            'jumlah'    => 25,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Project XYZ',
        ]);

        $response->assertSessionHasErrors(['jumlah']);
        $this->assertDatabaseMissing('barang_keluar', [
            'barang_id' => $this->barang->id,
            'jumlah'    => 25,
        ]);
    }

    /**
     * Skenario 9: Pemeriksaan Barang Keluar (Kepala Gudang Menyetujui)
     * Kepala Gudang menyetujui transaksi -> transaksi diproses menggunakan metode FIFO.
     * Lot masuk paling awal dikurangi dan stok agregat terpotong.
     */
    public function test_skenario_9_kepala_gudang_menyetujui_transaksi_dengan_alokasi_fifo(): void
    {
        // Dua lot masuk: Lot 1 (10 unit, tgl 1) dan Lot 2 (20 unit, tgl 5)
        $lot1 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'sumber'      => 'Lot 1 Supplier A',
        ]);
        $lot2 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-05',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Lot 2 Supplier B',
        ]);
        $this->barang->update(['stok' => 30]);

        // Buat pengajuan pending 15 unit
        $pengajuan = BarangKeluar::factory()->pending()->create([
            'barang_id' => $this->barang->id,
            'user_id'   => $this->staff->id,
            'jumlah'    => 15,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Produksi Spanduk Akbar',
        ]);

        // Kepala Gudang menyetujui transaksi
        $response = $this->actingAs($this->kepalaGudang)->post(route('inventory.transaksi.approval.approve', $pengajuan->id));

        $response->assertRedirect(route('inventory.transaksi.approval.index', ['tab' => 'pending']));
        $response->assertSessionHas('success');

        // Status berubah menjadi disetujui
        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);
        $this->assertEquals($this->kepalaGudang->id, $pengajuan->approved_by);
        $this->assertNotNull($pengajuan->approved_at);

        // Alokasi FIFO: Lot 1 habis (0 sisa), Lot 2 berkurang 5 (15 sisa), Stok total 15
        $lot1->refresh();
        $lot2->refresh();
        $this->barang->refresh();

        $this->assertEquals(0, $lot1->sisa_jumlah);
        $this->assertEquals(15, $lot2->sisa_jumlah);
        $this->assertEquals(15, $this->barang->stok);
        $this->assertCount(2, $pengajuan->details);
    }

    /**
     * Skenario 10: Pemeriksaan Barang Keluar (Kepala Gudang Menolak)
     * Kepala Gudang menolak transaksi dengan alasan -> transaksi tidak diproses dan stok tidak berubah.
     */
    public function test_skenario_10_kepala_gudang_menolak_transaksi_stok_tidak_berubah(): void
    {
        $lot = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->staff->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 40,
            'sisa_jumlah' => 40,
            'sumber'      => 'Supplier A',
        ]);
        $this->barang->update(['stok' => 40]);

        $pengajuan = BarangKeluar::factory()->pending()->create([
            'barang_id' => $this->barang->id,
            'user_id'   => $this->staff->id,
            'jumlah'    => 10,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Keperluan Internal',
        ]);

        // Kepala Gudang menolak transaksi
        $response = $this->actingAs($this->kepalaGudang)->post(route('inventory.transaksi.approval.reject', $pengajuan->id), [
            'catatan_penolakan' => 'Dokumen permohonan SPK belum ditandatangani Manajer Produksi',
        ]);

        $response->assertRedirect(route('inventory.transaksi.approval.index', ['tab' => 'pending']));
        $response->assertSessionHas('success');

        // Status berubah menjadi ditolak beserta alasan
        $pengajuan->refresh();
        $this->assertEquals('ditolak', $pengajuan->status);
        $this->assertEquals($this->kepalaGudang->id, $pengajuan->approved_by);
        $this->assertEquals('Dokumen permohonan SPK belum ditandatangani Manajer Produksi', $pengajuan->catatan_penolakan);
        $this->assertNotNull($pengajuan->approved_at);

        // Stok dan lot persediaan FIFO tetap utuh 100%
        $this->barang->refresh();
        $lot->refresh();
        $this->assertEquals(40, $this->barang->stok);
        $this->assertEquals(40, $lot->sisa_jumlah);
        $this->assertCount(0, $pengajuan->details);
    }

    /**
     * Skenario Otorisasi: Staff tidak berhak menyetujui atau menolak permohonan (RBAC Check).
     */
    public function test_staff_cannot_approve_or_reject_barang_keluar(): void
    {
        $pengajuan = BarangKeluar::factory()->pending()->create([
            'barang_id' => $this->barang->id,
            'user_id'   => $this->staff->id,
        ]);

        $responseApprove = $this->actingAs($this->staff)->post(route('inventory.transaksi.approval.approve', $pengajuan->id));
        $responseApprove->assertRedirect(route('inventory.dashboard'));

        $responseReject = $this->actingAs($this->staff)->post(route('inventory.transaksi.approval.reject', $pengajuan->id), [
            'catatan_penolakan' => 'Tolak',
        ]);
        $responseReject->assertRedirect(route('inventory.dashboard'));
    }
}
