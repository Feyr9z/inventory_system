<?php

namespace Tests\Unit;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\User;
use App\Services\FifoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class FifoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FifoService $fifoService;
    protected User $user;
    protected Barang $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fifoService = app(FifoService::class);
        $this->user = User::factory()->create();

        $kategori = Kategori::create(['nama_kategori' => 'Event Supplies']);
        $this->barang = Barang::create([
            'nama_barang'  => 'Spanduk Banner 3x1m',
            'kategori_id'  => $kategori->id,
            'stok'         => 0,
            'stok_minimum' => 5,
            'lokasi'       => 'Rak A1',
        ]);
    }

    /**
     * Skenario FIFO 1: Satu lot cukup, lot tertua berkurang dan detail tercatat.
     */
    public function test_fifo_satu_lot_cukup(): void
    {
        $lot1 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Supplier Alpha',
        ]);
        $this->barang->update(['stok' => 20]);

        $keluar = $this->fifoService->processBarangKeluar([
            'barang_id' => $this->barang->id,
            'jumlah'    => 8,
            'tanggal'   => '2026-08-05',
            'tujuan'    => 'Client A',
        ], $this->user->id);

        $lot1->refresh();
        $this->barang->refresh();

        $this->assertEquals(12, $lot1->sisa_jumlah);
        $this->assertEquals(12, $this->barang->stok);
        $this->assertCount(1, $keluar->details);
        $this->assertEquals(8, $keluar->details->first()->jumlah_diambil);
        $this->assertEquals($lot1->id, $keluar->details->first()->barang_masuk_id);
    }

    /**
     * Skenario FIFO 2: Satu lot tidak cukup, sistem lanjut mengambil dari lot berikutnya.
     */
    public function test_fifo_satu_lot_tidak_cukup_lanjut_ke_lot_berikutnya(): void
    {
        $lot1 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'sumber'      => 'Supplier Alpha',
        ]);
        $lot2 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-05',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Supplier Beta',
        ]);
        $this->barang->update(['stok' => 30]);

        // Keluar 18 unit (10 dari lot1, 8 dari lot2)
        $keluar = $this->fifoService->processBarangKeluar([
            'barang_id' => $this->barang->id,
            'jumlah'    => 18,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Project Launching',
        ], $this->user->id);

        $lot1->refresh();
        $lot2->refresh();
        $this->barang->refresh();

        $this->assertEquals(0, $lot1->sisa_jumlah, 'Lot 1 tertua harus habis (0)');
        $this->assertEquals(12, $lot2->sisa_jumlah, 'Lot 2 harus tersisa 12 (20 - 8)');
        $this->assertEquals(12, $this->barang->stok, 'Stok total harus 12');
        $this->assertCount(2, $keluar->details);
    }

    /**
     * Skenario FIFO 3: Pengeluaran melewati tiga lot berurutan.
     */
    public function test_fifo_pengeluaran_melewati_tiga_lot(): void
    {
        $lot1 = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-01', 'jumlah' => 5, 'sisa_jumlah' => 5, 'sumber' => 'S1']);
        $lot2 = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-03', 'jumlah' => 5, 'sisa_jumlah' => 5, 'sumber' => 'S2']);
        $lot3 = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-07', 'jumlah' => 10, 'sisa_jumlah' => 10, 'sumber' => 'S3']);
        $this->barang->update(['stok' => 20]);

        // Keluar 13 unit (5 dari lot1, 5 dari lot2, 3 dari lot3)
        $keluar = $this->fifoService->processBarangKeluar([
            'barang_id' => $this->barang->id,
            'jumlah'    => 13,
            'tanggal'   => '2026-08-15',
            'tujuan'    => 'Event Regional',
        ], $this->user->id);

        $lot1->refresh();
        $lot2->refresh();
        $lot3->refresh();
        $this->barang->refresh();

        $this->assertEquals(0, $lot1->sisa_jumlah);
        $this->assertEquals(0, $lot2->sisa_jumlah);
        $this->assertEquals(7, $lot3->sisa_jumlah);
        $this->assertEquals(7, $this->barang->stok);
        $this->assertCount(3, $keluar->details);
    }

    /**
     * Skenario FIFO 4: Tanggal masuk sama, memakai id ASC sebagai tie-breaker (PRD §5).
     */
    public function test_fifo_tanggal_sama_memakai_id_asc_sebagai_tie_breaker(): void
    {
        $lotA = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-10', 'jumlah' => 10, 'sisa_jumlah' => 10, 'sumber' => 'A']);
        $lotB = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-10', 'jumlah' => 10, 'sisa_jumlah' => 10, 'sumber' => 'B']);
        $this->barang->update(['stok' => 20]);

        // Keluar 12 unit -> 10 dari lotA (ID lebih kecil), 2 dari lotB
        $this->fifoService->processBarangKeluar([
            'barang_id' => $this->barang->id,
            'jumlah'    => 12,
            'tanggal'   => '2026-08-11',
            'tujuan'    => 'Event X',
        ], $this->user->id);

        $lotA->refresh();
        $lotB->refresh();

        $this->assertEquals(0, $lotA->sisa_jumlah);
        $this->assertEquals(8, $lotB->sisa_jumlah);
    }

    /**
     * Skenario FIFO 5: Pengeluaran melebihi total stok yang tersedia ditolak.
     */
    public function test_fifo_pengeluaran_melebihi_stok_tersedia_ditolak(): void
    {
        BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-01', 'jumlah' => 5, 'sisa_jumlah' => 5, 'sumber' => 'S1']);
        $this->barang->update(['stok' => 5]);

        $this->expectException(InvalidArgumentException::class);

        $this->fifoService->processBarangKeluar([
            'barang_id' => $this->barang->id,
            'jumlah'    => 10, // Melebihi stok 5
            'tanggal'   => '2026-08-05',
            'tujuan'    => 'Event Y',
        ], $this->user->id);
    }

    /**
     * Skenario FIFO 6: Sisa lot dan stok agregat konsisten 100%.
     */
    public function test_fifo_sisa_lot_dan_stok_agregat_konsisten(): void
    {
        BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-01', 'jumlah' => 15, 'sisa_jumlah' => 15, 'sumber' => 'S1']);
        BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-02', 'jumlah' => 25, 'sisa_jumlah' => 25, 'sumber' => 'S2']);
        $this->barang->update(['stok' => 40]);

        $this->fifoService->processBarangKeluar([
            'barang_id' => $this->barang->id,
            'jumlah'    => 17,
            'tanggal'   => '2026-08-03',
            'tujuan'    => 'Client C',
        ], $this->user->id);

        $this->barang->refresh();
        $totalSisaLots = BarangMasuk::where('barang_id', $this->barang->id)->sum('sisa_jumlah');

        $this->assertEquals($this->barang->stok, $totalSisaLots);
        $this->assertEquals(23, $this->barang->stok);
    }

    /**
     * Skenario FIFO 7: Stock Opname Surplus membuat lot penyesuaian baru.
     */
    public function test_fifo_stock_opname_surplus_membuat_lot_penyesuaian(): void
    {
        BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-01', 'jumlah' => 10, 'sisa_jumlah' => 10, 'sumber' => 'S1']);
        $this->barang->update(['stok' => 10]);

        // Hasil fisik 14 (+4 surplus)
        $selisih = $this->fifoService->adjustLotsForStockOpname($this->barang, 14, '2026-08-30', $this->user->id);

        $this->barang->refresh();
        $this->assertEquals(4, $selisih);
        $this->assertEquals(14, $this->barang->stok);

        $adjustmentLot = BarangMasuk::where('barang_id', $this->barang->id)->latest('id')->first();
        $this->assertEquals(4, $adjustmentLot->sisa_jumlah);
        $this->assertStringContainsString('Surplus', $adjustmentLot->sumber);
    }

    /**
     * Skenario FIFO 8: Stock Opname Defisit memotong lot tertua secara berurutan.
     */
    public function test_fifo_stock_opname_defisit_memotong_lot_tertua(): void
    {
        $lot1 = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-01', 'jumlah' => 10, 'sisa_jumlah' => 10, 'sumber' => 'S1']);
        $lot2 = BarangMasuk::create(['barang_id' => $this->barang->id, 'user_id' => $this->user->id, 'tanggal' => '2026-08-05', 'jumlah' => 10, 'sisa_jumlah' => 10, 'sumber' => 'S2']);
        $this->barang->update(['stok' => 20]);

        // Hasil fisik 16 (-4 defisit) -> kurangi 4 dari lot1 tertua
        $selisih = $this->fifoService->adjustLotsForStockOpname($this->barang, 16, '2026-08-30', $this->user->id);

        $lot1->refresh();
        $lot2->refresh();
        $this->barang->refresh();

        $this->assertEquals(-4, $selisih);
        $this->assertEquals(16, $this->barang->stok);
        $this->assertEquals(6, $lot1->sisa_jumlah);
        $this->assertEquals(10, $lot2->sisa_jumlah);
    }

    /**
     * Skenario Approval 1: Pengajuan barang keluar berstatus pending tanpa mengurangi stok atau lot.
     */
    public function test_submit_pengajuan_creates_pending_status_without_reducing_stock_or_lots(): void
    {
        $lot = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 30,
            'sisa_jumlah' => 30,
            'sumber'      => 'Supplier Alpha',
        ]);
        $this->barang->update(['stok' => 30]);

        $pengajuan = $this->fifoService->submitPengajuan([
            'barang_id' => $this->barang->id,
            'jumlah'    => 10,
            'tanggal'   => '2026-08-05',
            'tujuan'    => 'Proyek A',
        ], $this->user->id);

        $this->assertEquals('pending', $pengajuan->status);
        $this->assertNull($pengajuan->approved_by);
        $this->assertNull($pengajuan->approved_at);

        // Pastikan stok dan lot belum terpotong
        $this->barang->refresh();
        $lot->refresh();
        $this->assertEquals(30, $this->barang->stok);
        $this->assertEquals(30, $lot->sisa_jumlah);
        $this->assertEquals(0, $pengajuan->details()->count());
    }

    /**
     * Skenario Approval 2: Persetujuan Kepala Gudang mengeksekusi FIFO dan mengurangi stok/lot.
     */
    public function test_approve_pengajuan_executes_fifo_and_reduces_stock_and_lots(): void
    {
        $approver = User::factory()->kepalaGudang()->create();

        $lot1 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'sumber'      => 'Lot 1',
        ]);
        $lot2 = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-05',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Lot 2',
        ]);
        $this->barang->update(['stok' => 30]);

        $pengajuan = $this->fifoService->submitPengajuan([
            'barang_id' => $this->barang->id,
            'jumlah'    => 15,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Proyek Billboard',
        ], $this->user->id);

        $approved = $this->fifoService->approvePengajuan($pengajuan->id, $approver->id);

        $this->assertEquals('disetujui', $approved->status);
        $this->assertEquals($approver->id, $approved->approved_by);
        $this->assertNotNull($approved->approved_at);

        // Verifikasi alokasi FIFO (10 dari lot1, 5 dari lot2)
        $lot1->refresh();
        $lot2->refresh();
        $this->barang->refresh();

        $this->assertEquals(0, $lot1->sisa_jumlah);
        $this->assertEquals(15, $lot2->sisa_jumlah);
        $this->assertEquals(15, $this->barang->stok);
        $this->assertEquals(2, $approved->details()->count());
    }

    /**
     * Skenario Approval 3: Penolakan Kepala Gudang menandai ditolak tanpa mengubah stok atau lot.
     */
    public function test_reject_pengajuan_marks_ditolak_with_reason_without_changing_stock(): void
    {
        $approver = User::factory()->kepalaGudang()->create();

        $lot = BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 20,
            'sisa_jumlah' => 20,
            'sumber'      => 'Lot 1',
        ]);
        $this->barang->update(['stok' => 20]);

        $pengajuan = $this->fifoService->submitPengajuan([
            'barang_id' => $this->barang->id,
            'jumlah'    => 10,
            'tanggal'   => '2026-08-10',
            'tujuan'    => 'Proyek B',
        ], $this->user->id);

        $rejected = $this->fifoService->rejectPengajuan($pengajuan->id, $approver->id, 'Dokumen proyek belum lengkap');

        $this->assertEquals('ditolak', $rejected->status);
        $this->assertEquals($approver->id, $rejected->approved_by);
        $this->assertEquals('Dokumen proyek belum lengkap', $rejected->catatan_penolakan);
        $this->assertNotNull($rejected->approved_at);

        // Stok dan lot tetap utuh
        $this->barang->refresh();
        $lot->refresh();
        $this->assertEquals(20, $this->barang->stok);
        $this->assertEquals(20, $lot->sisa_jumlah);
        $this->assertEquals(0, $rejected->details()->count());
    }

    /**
     * Skenario Approval 4: Pencegahan race condition saat approval jika stok mendadak tidak cukup.
     */
    public function test_approve_pengajuan_fails_when_stock_insufficient_at_approval_time(): void
    {
        $approver = User::factory()->kepalaGudang()->create();

        BarangMasuk::create([
            'barang_id'   => $this->barang->id,
            'user_id'     => $this->user->id,
            'tanggal'     => '2026-08-01',
            'jumlah'      => 10,
            'sisa_jumlah' => 10,
            'sumber'      => 'Lot 1',
        ]);
        $this->barang->update(['stok' => 10]);

        $pengajuan = $this->fifoService->submitPengajuan([
            'barang_id' => $this->barang->id,
            'jumlah'    => 10,
            'tanggal'   => '2026-08-05',
            'tujuan'    => 'Proyek C',
        ], $this->user->id);

        // Simulasikan transaksi lain menghabiskan stok sebelum pengajuan disetujui
        $this->barang->update(['stok' => 2]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stok barang saat ini tidak mencukupi');

        $this->fifoService->approvePengajuan($pengajuan->id, $approver->id);
    }
}
