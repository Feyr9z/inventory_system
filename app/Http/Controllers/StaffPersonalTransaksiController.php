<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffPersonalTransaksiController extends Controller
{
    /**
     * Menampilkan riwayat transaksi operasional personal milik staff yang sedang login.
     */
    public function index(Request $request)
    {
        $userId         = Auth::id();
        $user           = Auth::user();
        $dari_tanggal   = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $tipe_transaksi = $request->input('tipe_transaksi', 'semua');
        $search         = $request->input('search');
        $sort           = $request->input('sort', 'tanggal_desc');

        $data = [];
        $total_masuk  = 0;
        $total_keluar = 0;

        // 1. Barang Masuk milik user ini
        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $queryMasuk = BarangMasuk::with(['barang.kategori', 'user'])
                ->where('user_id', $userId)
                ->whereDate('tanggal', '>=', $dari_tanggal)
                ->whereDate('tanggal', '<=', $sampai_tanggal);

            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if ($search) {
                $queryMasuk->where(function ($q) use ($search, $like) {
                    $q->whereHas('barang', function ($b) use ($search, $like) {
                        $b->where('nama_barang', $like, '%' . $search . '%');
                    })->orWhere('sumber', $like, '%' . $search . '%');
                });
            }

            $masuk = $queryMasuk->get()->map(function ($item) use (&$total_masuk) {
                $total_masuk += $item->jumlah;
                $docNumber = 'IN-' . $item->tanggal->format('Ymd') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);

                return [
                    'id'          => $item->id,
                    'doc_number'  => $docNumber,
                    'tanggal'     => $item->tanggal->format('Y-m-d'),
                    'tanggal_fmt' => $item->tanggal->format('d/m/Y'),
                    'waktu_input' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : $item->tanggal->format('d/m/Y'),
                    'tipe'        => 'Masuk',
                    'nama_barang' => $item->barang?->nama_barang ?? 'Barang #' . $item->barang_id,
                    'kategori'    => $item->barang?->kategori?->nama_kategori ?? '-',
                    'lokasi'      => $item->barang?->lokasi ?? 'Gudang Utama',
                    'jumlah'      => $item->jumlah,
                    'sisa_jumlah' => $item->sisa_jumlah,
                    'keterangan'  => $item->sumber,
                    'petugas'     => $item->user?->name ?? 'Saya',
                    'petugas_role'=> $item->user ? (\App\Enums\Role::tryFrom($item->user->role)?->label() ?? ucfirst($item->user->role)) : 'Staff',
                    'fifo_info'   => null,
                    'fifo_details'=> null,
                ];
            });

            $data = array_merge($data, $masuk->toArray());
        }

        // 2. Barang Keluar milik user ini
        if ($tipe_transaksi === 'keluar' || $tipe_transaksi === 'semua') {
            $queryKeluar = BarangKeluar::with(['barang.kategori', 'user', 'details.barangMasuk'])
                ->where('user_id', $userId)
                ->whereDate('tanggal', '>=', $dari_tanggal)
                ->whereDate('tanggal', '<=', $sampai_tanggal);

            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if ($search) {
                $queryKeluar->where(function ($q) use ($search, $like) {
                    $q->whereHas('barang', function ($b) use ($search, $like) {
                        $b->where('nama_barang', $like, '%' . $search . '%');
                    })->orWhere('tujuan', $like, '%' . $search . '%');
                });
            }

            $keluar = $queryKeluar->get()->map(function ($item) use (&$total_keluar) {
                $total_keluar += $item->jumlah;
                $docNumber = 'OUT-' . $item->tanggal->format('Ymd') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);

                $fifoDetails = $item->details->map(function ($d) {
                    return [
                        'lot_id'         => $d->barang_masuk_id,
                        'lot_tanggal'    => $d->barangMasuk?->tanggal?->format('d/m/Y') ?? '-',
                        'lot_sumber'     => $d->barangMasuk?->sumber ?? '-',
                        'jumlah_diambil' => $d->jumlah_diambil,
                    ];
                })->all();

                $fifoBreakdown = $item->details->map(function ($detail) {
                    $lotDate = $detail->barangMasuk?->tanggal?->format('d/m/Y') ?? '-';
                    $lotSource = $detail->barangMasuk?->sumber ?? '-';
                    return "Lot #{$detail->barang_masuk_id} ({$lotDate}, {$lotSource}): {$detail->jumlah_diambil} unit";
                })->all();

                return [
                    'id'          => $item->id,
                    'doc_number'  => $docNumber,
                    'tanggal'     => $item->tanggal->format('Y-m-d'),
                    'tanggal_fmt' => $item->tanggal->format('d/m/Y'),
                    'waktu_input' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : $item->tanggal->format('d/m/Y'),
                    'tipe'        => 'Keluar',
                    'nama_barang' => $item->barang?->nama_barang ?? 'Barang #' . $item->barang_id,
                    'kategori'    => $item->barang?->kategori?->nama_kategori ?? '-',
                    'lokasi'      => $item->barang?->lokasi ?? 'Gudang Utama',
                    'jumlah'      => -$item->jumlah,
                    'sisa_jumlah' => null,
                    'keterangan'  => $item->tujuan,
                    'petugas'     => $item->user?->name ?? 'Saya',
                    'petugas_role'=> $item->user ? (\App\Enums\Role::tryFrom($item->user->role)?->label() ?? ucfirst($item->user->role)) : 'Staff',
                    'fifo_info'   => $fifoBreakdown,
                    'fifo_details'=> $fifoDetails,
                ];
            });

            $data = array_merge($data, $keluar->toArray());
        }

        // Sorting
        usort($data, function ($a, $b) use ($sort) {
            $timeA = strtotime($a['tanggal']);
            $timeB = strtotime($b['tanggal']);

            if ($sort === 'tanggal_asc') {
                return $timeA === $timeB ? ($a['id'] <=> $b['id']) : ($timeA <=> $timeB);
            }

            return $timeA === $timeB ? ($b['id'] <=> $a['id']) : ($timeB <=> $timeA);
        });

        $total_transaksi = count($data);

        return view('transaksi.personal', compact(
            'data',
            'user',
            'dari_tanggal',
            'sampai_tanggal',
            'tipe_transaksi',
            'search',
            'sort',
            'total_transaksi',
            'total_masuk',
            'total_keluar'
        ));
    }
}
