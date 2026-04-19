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

        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $masuk = BarangMasuk::with('barang')
                ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
                ->get()
                ->map(function ($item) {
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
                ->map(function ($item) {
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

        return view('laporan.transaksi', compact('data', 'dari_tanggal', 'sampai_tanggal', 'tipe_transaksi'));
    }

    public function stok(Request $request)
    {
        $barang = Barang::with('kategori')
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
            });

        return view('laporan.stok', compact('barang'));
    }
}
