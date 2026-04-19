<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function transaksi(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $tipe_transaksi = $request->input('tipe_transaksi', 'semua');

        $data = [];
        $total_masuk = 0;
        $total_keluar = 0;

        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $masuk = BarangMasuk::with('barang')
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$total_masuk) {
                    $total_masuk += $item->jumlah;
                    return [
                        'tanggal' => $item->tanggal,
                        'tipe' => 'Masuk',
                        'nama_barang' => $item->barang->nama_barang,
                        'jumlah' => $item->jumlah,
                        'keterangan' => $item->sumber,
                    ];
                });
            $data = array_merge($data, $masuk->toArray());
        }

        if ($tipe_transaksi === 'keluar' || $tipe_transaksi === 'semua') {
            $keluar = BarangKeluar::with('barang')
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$total_keluar) {
                    $total_keluar += $item->jumlah;
                    return [
                        'tanggal' => $item->tanggal,
                        'tipe' => 'Keluar',
                        'nama_barang' => $item->barang->nama_barang,
                        'jumlah' => -$item->jumlah,
                        'keterangan' => $item->tujuan,
                    ];
                });
            $data = array_merge($data, $keluar->toArray());
        }

        // Sort by tanggal
        usort($data, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        return view('laporan.transaksi', compact('data', 'dari_tanggal', 'sampai_tanggal', 'tipe_transaksi', 'total_masuk', 'total_keluar'));
    }

    public function stok(Request $request)
    {
        $kategori_id = $request->input('kategori_id', null);
        $status = $request->input('status', null);

        $barang = Barang::with('kategori')
            ->when($kategori_id, function ($query) use ($kategori_id) {
                return $query->where('kategori_id', $kategori_id);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_barang' => $item->nama_barang,
                    'kategori' => $item->kategori->nama_kategori,
                    'stok' => $item->stok,
                    'stok_minimum' => $item->stok_minimum,
                    'status' => $item->stok < $item->stok_minimum ? 'Kurang' : 'Normal',
                    'lokasi' => $item->lokasi,
                ];
            })
            ->toArray();

        // Filter by status if provided
        if ($status && $status !== 'semua') {
            $barang = array_filter($barang, function ($item) use ($status) {
                return $item['status'] === ucfirst($status);
            });
            // Re-index array after filtering
            $barang = array_values($barang);
        }

        $kategori = \App\Models\Kategori::all();

        return view('laporan.stok', compact('barang', 'kategori', 'kategori_id', 'status'));
    }
}
