<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan transaksi barang masuk & keluar dengan informasi detail FIFO.
     */
    public function transaksi(Request $request)
    {
        $dari_tanggal   = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $tipe_transaksi = $request->input('tipe_transaksi', 'semua');

        $data = [];
        $total_masuk  = 0;
        $total_keluar = 0;

        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $masuk = BarangMasuk::with(['barang', 'user'])
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$total_masuk) {
                    $total_masuk += $item->jumlah;
                    return [
                        'id'          => $item->id,
                        'tanggal'     => $item->tanggal->format('Y-m-d'),
                        'tipe'        => 'Masuk',
                        'nama_barang' => $item->barang?->nama_barang ?? 'Barang #' . $item->barang_id,
                        'jumlah'      => $item->jumlah,
                        'sisa_jumlah' => $item->sisa_jumlah,
                        'keterangan'  => $item->sumber,
                        'petugas'     => $item->user?->name ?? '-',
                        'fifo_info'   => null,
                    ];
                });
            $data = array_merge($data, $masuk->toArray());
        }

        if ($tipe_transaksi === 'keluar' || $tipe_transaksi === 'semua') {
            $keluar = BarangKeluar::with(['barang', 'user', 'details.barangMasuk'])
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$total_keluar) {
                    $total_keluar += $item->jumlah;

                    // Buat ringkasan alokasi lot FIFO
                    $fifoBreakdown = $item->details->map(function ($detail) {
                        $lotDate = $detail->barangMasuk?->tanggal?->format('d/m/Y') ?? '-';
                        $lotSource = $detail->barangMasuk?->sumber ?? '-';
                        return "Lot #{$detail->barang_masuk_id} ({$lotDate}, {$lotSource}): {$detail->jumlah_diambil} unit";
                    })->all();

                    return [
                        'id'          => $item->id,
                        'tanggal'     => $item->tanggal->format('Y-m-d'),
                        'tipe'        => 'Keluar',
                        'nama_barang' => $item->barang?->nama_barang ?? 'Barang #' . $item->barang_id,
                        'jumlah'      => -$item->jumlah,
                        'sisa_jumlah' => null,
                        'keterangan'  => $item->tujuan,
                        'petugas'     => $item->user?->name ?? '-',
                        'fifo_info'   => $fifoBreakdown,
                    ];
                });
            $data = array_merge($data, $keluar->toArray());
        }

        // Urutkan berdasarkan tanggal ASC
        usort($data, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        return view('laporan.transaksi', compact(
            'data',
            'dari_tanggal',
            'sampai_tanggal',
            'tipe_transaksi',
            'total_masuk',
            'total_keluar'
        ));
    }

    /**
     * Menampilkan laporan posisi stok dengan filtering efisien langsung pada SQL query (PRD §19 SHOULD CHANGE #3).
     */
    public function stok(Request $request)
    {
        $kategori_id = $request->input('kategori_id');
        $status      = $request->input('status');

        $query = Barang::with(['kategori', 'barangMasuk' => function ($q) {
            $q->where('sisa_jumlah', '>', 0)->orderBy('tanggal', 'asc');
        }]);

        if ($kategori_id) {
            $query->where('kategori_id', $kategori_id);
        }

        if ($status === 'kurang') {
            $query->whereRaw('stok < stok_minimum');
        } elseif ($status === 'normal') {
            $query->whereRaw('stok >= stok_minimum');
        }

        $barang   = $query->orderBy('nama_barang', 'asc')->paginate(15)->withQueryString();
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('laporan.stok', compact('barang', 'kategori', 'kategori_id', 'status'));
    }

    /**
     * Export laporan transaksi ke CSV dengan informasi petugas dan detail FIFO.
     */
    public function exportTransaksiCsv(Request $request)
    {
        $dari_tanggal   = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $tipe_transaksi = $request->input('tipe_transaksi', 'semua');

        $data = [];

        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $masuk = BarangMasuk::with(['barang', 'user'])
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal'     => $item->tanggal->format('Y-m-d'),
                        'tipe'        => 'Masuk',
                        'nama_barang' => $item->barang?->nama_barang ?? '-',
                        'jumlah'      => $item->jumlah,
                        'keterangan'  => $item->sumber,
                        'petugas'     => $item->user?->name ?? '-',
                        'alokasi_fifo'=> "Lot ID: {$item->id} (Sisa: {$item->sisa_jumlah})",
                    ];
                });
            $data = array_merge($data, $masuk->toArray());
        }

        if ($tipe_transaksi === 'keluar' || $tipe_transaksi === 'semua') {
            $keluar = BarangKeluar::with(['barang', 'user', 'details.barangMasuk'])
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) {
                    $fifoBreakdown = $item->details->map(function ($d) {
                        return "Lot #{$d->barang_masuk_id} ({$d->jumlah_diambil} unit)";
                    })->implode('; ');

                    return [
                        'tanggal'     => $item->tanggal->format('Y-m-d'),
                        'tipe'        => 'Keluar',
                        'nama_barang' => $item->barang?->nama_barang ?? '-',
                        'jumlah'      => $item->jumlah,
                        'keterangan'  => $item->tujuan,
                        'petugas'     => $item->user?->name ?? '-',
                        'alokasi_fifo'=> $fifoBreakdown ?: 'FIFO',
                    ];
                });
            $data = array_merge($data, $keluar->toArray());
        }

        usort($data, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $filename = 'laporan-transaksi-' . $dari_tanggal . '-to-' . $sampai_tanggal . '.csv';

        return new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($handle, ['Tanggal', 'Tipe', 'Nama Barang', 'Jumlah Unit', 'Keterangan / Tujuan / Sumber', 'Petugas Input', 'Alokasi Lot FIFO']);

            // CSV Data Rows
            foreach ($data as $item) {
                fputcsv($handle, [
                    $item['tanggal'],
                    $item['tipe'],
                    $item['nama_barang'],
                    $item['jumlah'],
                    $item['keterangan'],
                    $item['petugas'],
                    $item['alokasi_fifo'],
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export laporan posisi stok ke CSV dengan filtering SQL.
     */
    public function exportStokCsv(Request $request)
    {
        $kategori_id = $request->input('kategori_id');
        $status      = $request->input('status');

        $query = Barang::with('kategori');

        if ($kategori_id) {
            $query->where('kategori_id', $kategori_id);
        }

        if ($status === 'kurang') {
            $query->whereRaw('stok < stok_minimum');
        } elseif ($status === 'normal') {
            $query->whereRaw('stok >= stok_minimum');
        }

        $barang = $query->orderBy('nama_barang', 'asc')->get();
        $filename = 'laporan-stok-' . now()->format('Y-m-d-His') . '.csv';

        return new StreamedResponse(function () use ($barang) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Nama Barang', 'Kategori', 'Stok Saat Ini', 'Stok Minimum', 'Status Stok', 'Lokasi Gudang']);

            foreach ($barang as $item) {
                fputcsv($handle, [
                    $item->nama_barang,
                    $item->kategori?->nama_kategori ?? '-',
                    $item->stok,
                    $item->stok_minimum,
                    $item->stok < $item->stok_minimum ? 'Kurang (Perlu Restock)' : 'Normal (Aman)',
                    $item->lokasi ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
