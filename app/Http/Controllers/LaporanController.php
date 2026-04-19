<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Build query
        $query = Barang::with('kategori')
            ->when($kategori_id, function ($query) use ($kategori_id) {
                return $query->where('kategori_id', $kategori_id);
            });

        // Get all for status filtering
        $allBarang = $query->get()
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
            $allBarang = array_filter($allBarang, function ($item) use ($status) {
                return $item['status'] === ucfirst($status);
            });
            $allBarang = array_values($allBarang);
        }

        // Paginate
        $page = request('page', 1);
        $perPage = 15;
        $total = count($allBarang);
        $barang = array_slice($allBarang, ($page - 1) * $perPage, $perPage);
        
        // Create paginator instance
        $barang = new \Illuminate\Pagination\Paginator(
            $barang,
            $perPage,
            $page,
            [
                'path' => route('inventory.laporan.stok'),
                'query' => $request->query(),
            ]
        );

        $kategori = \App\Models\Kategori::all();

        return view('laporan.stok', compact('barang', 'kategori', 'kategori_id', 'status'));
    }

    public function exportTransaksiCsv(Request $request)
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
                        'jumlah' => $item->jumlah,
                        'keterangan' => $item->tujuan,
                    ];
                });
            $data = array_merge($data, $keluar->toArray());
        }

        usort($data, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $filename = 'laporan-transaksi-' . $dari_tanggal . '-to-' . $sampai_tanggal . '.csv';

        $response = new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, ['Tanggal', 'Tipe', 'Nama Barang', 'Jumlah', 'Keterangan']);
            
            // Data
            foreach ($data as $item) {
                fputcsv($handle, [
                    $item['tanggal'],
                    $item['tipe'],
                    $item['nama_barang'],
                    $item['jumlah'],
                    $item['keterangan'],
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

        return $response;
    }

    public function exportStokCsv(Request $request)
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
                    'nama_barang' => $item->nama_barang,
                    'kategori' => $item->kategori->nama_kategori,
                    'stok' => $item->stok,
                    'stok_minimum' => $item->stok_minimum,
                    'status' => $item->stok < $item->stok_minimum ? 'Kurang' : 'Normal',
                    'lokasi' => $item->lokasi ?? '-',
                ];
            })
            ->toArray();

        if ($status && $status !== 'semua') {
            $barang = array_filter($barang, function ($item) use ($status) {
                return $item['status'] === ucfirst($status);
            });
            $barang = array_values($barang);
        }

        $filename = 'laporan-stok-' . now()->format('Y-m-d-His') . '.csv';

        $response = new StreamedResponse(function () use ($barang) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, ['Nama Barang', 'Kategori', 'Stok', 'Stok Minimum', 'Status', 'Lokasi']);
            
            // Data
            foreach ($barang as $item) {
                fputcsv($handle, [
                    $item['nama_barang'],
                    $item['kategori'],
                    $item['stok'],
                    $item['stok_minimum'],
                    $item['status'],
                    $item['lokasi'],
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

        return $response;
    }
}
