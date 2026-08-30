<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FifoService
{
    /**
     * Memproses pengeluaran barang dengan metode FIFO (First In First Out).
     *
     * Aturan:
     * - Lot masuk diurutkan berdasarkan tanggal ASC, id ASC (tie-breaker)
     * - Menggunakan pessimistic locking (lockForUpdate) untuk mencegah race condition
     * - Mengurangi sisa_jumlah pada lot masuk tertua yang masih memiliki sisa stok
     * - Mencatat riwayat pemakaian lot ke tabel barang_keluar_detail
     * - Mengurangi stok agregat pada barang
     * - Semua operasi dieksekusi secara atomic dalam DB::transaction
     *
     * @param array $data ['barang_id' => int, 'jumlah' => int, 'tanggal' => string, 'tujuan' => string]
     * @param int|null $userId ID pengguna yang melakukan transaksi
     * @return BarangKeluar
     * @throws InvalidArgumentException
     */
    public function processBarangKeluar(array $data, ?int $userId = null): BarangKeluar
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Lock record barang untuk mencegah race condition
            $barang = Barang::where('id', $data['barang_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $jumlahKeluar = (int) $data['jumlah'];

            // 2. Validasi ketersediaan stok agregat
            if ($barang->stok < $jumlahKeluar) {
                throw new InvalidArgumentException(
                    "Stok tidak cukup. Stok tersedia: {$barang->stok}, permintaan: {$jumlahKeluar}"
                );
            }

            // 3. Ambil lot aktif (sisa_jumlah > 0) berurutan FIFO (tanggal ASC, id ASC) dengan lock
            $activeLots = BarangMasuk::where('barang_id', $barang->id)
                ->where('sisa_jumlah', '>', 0)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $totalSisaLot = $activeLots->sum('sisa_jumlah');
            if ($totalSisaLot < $jumlahKeluar) {
                throw new InvalidArgumentException(
                    "Konsistensi lot tidak mencukupi. Sisa lot aktif: {$totalSisaLot}, permintaan: {$jumlahKeluar}"
                );
            }

            // 4. Simpan master transaksi barang_keluar
            $barangKeluar = BarangKeluar::create([
                'barang_id' => $barang->id,
                'user_id'   => $userId,
                'tanggal'   => $data['tanggal'],
                'jumlah'    => $jumlahKeluar,
                'tujuan'    => $data['tujuan'],
            ]);

            // 5. Alokasi FIFO: konsumsi sisa_jumlah dari lot tertua ke terbaru
            $remaining = $jumlahKeluar;

            foreach ($activeLots as $lot) {
                if ($remaining <= 0) {
                    break;
                }

                $qtyAmbil = min($remaining, $lot->sisa_jumlah);

                // Kurangi sisa_jumlah pada lot masuk
                $lot->sisa_jumlah -= $qtyAmbil;
                $lot->save();

                // Catat detail konsumsi lot
                BarangKeluarDetail::create([
                    'barang_keluar_id' => $barangKeluar->id,
                    'barang_masuk_id'  => $lot->id,
                    'jumlah_diambil'   => $qtyAmbil,
                ]);

                $remaining -= $qtyAmbil;
            }

            // 6. Kurangi stok agregat pada tabel barang
            $barang->stok -= $jumlahKeluar;
            $barang->save();

            return $barangKeluar;
        });
    }

    /**
     * Menjaga konsistensi lot FIFO saat Stock Opname dilakukan (PRD §7).
     *
     * Aturan:
     * - Selisih positif (+): Dibuat lot penyesuaian baru (BarangMasuk) dengan sisa_jumlah = selisih
     * - Selisih negatif (-): Dikurangi dari lot tertua secara berurutan sampai selisih terpenuhi
     * - Stok agregat disesuaikan dengan stok fisik
     *
     * @param Barang $barang
     * @param int $stokFisik
     * @param string $tanggal
     * @param int|null $userId
     * @return int Nilai selisih (stok_fisik - stok_sistem)
     */
    public function adjustLotsForStockOpname(Barang $barang, int $stokFisik, string $tanggal, ?int $userId = null): int
    {
        return DB::transaction(function () use ($barang, $stokFisik, $tanggal, $userId) {
            // Lock record barang
            $lockedBarang = Barang::where('id', $barang->id)->lockForUpdate()->firstOrFail();
            $stokSistem = $lockedBarang->stok;
            $selisih = $stokFisik - $stokSistem;

            if ($selisih > 0) {
                // Surplus: Tambah lot baru dengan sisa_jumlah = selisih
                BarangMasuk::create([
                    'barang_id'   => $lockedBarang->id,
                    'user_id'     => $userId,
                    'tanggal'     => $tanggal,
                    'jumlah'      => $selisih,
                    'sisa_jumlah' => $selisih,
                    'sumber'      => 'Penyesuaian Stock Opname (Surplus)',
                ]);
            } elseif ($selisih < 0) {
                // Defisit: Kurangi sisa_jumlah dari lot FIFO tertua
                $deficitToReduce = abs($selisih);

                $activeLots = BarangMasuk::where('barang_id', $lockedBarang->id)
                    ->where('sisa_jumlah', '>', 0)
                    ->orderBy('tanggal', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($activeLots as $lot) {
                    if ($deficitToReduce <= 0) {
                        break;
                    }

                    $reduce = min($deficitToReduce, $lot->sisa_jumlah);
                    $lot->sisa_jumlah -= $reduce;
                    $lot->save();

                    $deficitToReduce -= $reduce;
                }
            }

            // Update stok agregat
            $lockedBarang->stok = $stokFisik;
            $lockedBarang->save();

            return $selisih;
        });
    }
}
