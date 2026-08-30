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
     * Menampilkan laporan transaksi barang masuk & keluar dengan sorting dan filtering interaktif.
     */
    public function transaksi(Request $request)
    {
        $dari_tanggal   = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $tipe_transaksi = $request->input('tipe_transaksi', 'semua');
        $search         = $request->input('search');
        $sort           = $request->input('sort', 'tanggal_desc');

        $data = [];
        $total_masuk  = 0;
        $total_keluar = 0;

        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $queryMasuk = BarangMasuk::with(['barang.kategori', 'user'])
                ->whereDate('tanggal', '>=', $dari_tanggal)
                ->whereDate('tanggal', '<=', $sampai_tanggal);

            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if ($search) {
                $queryMasuk->where(function ($q) use ($search, $like) {
                    $q->whereHas('barang', function ($b) use ($search, $like) {
                        $b->where('nama_barang', $like, '%' . $search . '%');
                    })->orWhere('sumber', $like, '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search, $like) {
                        $u->where('name', $like, '%' . $search . '%');
                    });
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
                    'petugas'     => $item->user?->name ?? '-',
                    'petugas_role'=> $item->user ? (\App\Enums\Role::tryFrom($item->user->role)?->label() ?? ucfirst($item->user->role)) : '-',
                    'fifo_info'   => null,
                    'fifo_details'=> null,
                ];
            });

            $data = array_merge($data, $masuk->toArray());
        }

        if ($tipe_transaksi === 'keluar' || $tipe_transaksi === 'semua') {
            $queryKeluar = BarangKeluar::with(['barang.kategori', 'user', 'details.barangMasuk'])
                ->whereDate('tanggal', '>=', $dari_tanggal)
                ->whereDate('tanggal', '<=', $sampai_tanggal);

            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if ($search) {
                $queryKeluar->where(function ($q) use ($search, $like) {
                    $q->whereHas('barang', function ($b) use ($search, $like) {
                        $b->where('nama_barang', $like, '%' . $search . '%');
                    })->orWhere('tujuan', $like, '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search, $like) {
                        $u->where('name', $like, '%' . $search . '%');
                    });
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
                    'petugas'     => $item->user?->name ?? '-',
                    'petugas_role'=> $item->user ? (\App\Enums\Role::tryFrom($item->user->role)?->label() ?? ucfirst($item->user->role)) : '-',
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

            // Default: tanggal_desc (terbaru di atas)
            return $timeA === $timeB ? ($b['id'] <=> $a['id']) : ($timeB <=> $timeA);
        });

        return view('laporan.transaksi', compact(
            'data',
            'dari_tanggal',
            'sampai_tanggal',
            'tipe_transaksi',
            'search',
            'sort',
            'total_masuk',
            'total_keluar'
        ));
    }

    /**
     * Menampilkan laporan posisi stok dengan pencarian dan sorting fleksibel.
     */
    public function stok(Request $request)
    {
        $kategori_id = $request->input('kategori_id');
        $status      = $request->input('status');
        $search      = $request->input('search');
        $sort        = $request->input('sort', 'terbaru');

        $query = Barang::with(['kategori', 'barangMasuk' => function ($q) {
            $q->where('sisa_jumlah', '>', 0)->orderBy('tanggal', 'asc');
        }]);

        if ($search) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('nama_barang', $like, '%' . $search . '%')
                  ->orWhere('lokasi', $like, '%' . $search . '%');
            });
        }

        if ($kategori_id) {
            $query->where('kategori_id', $kategori_id);
        }

        if ($status === 'kurang') {
            $query->whereRaw('stok < stok_minimum');
        } elseif ($status === 'normal') {
            $query->whereRaw('stok >= stok_minimum');
        }

        match ($sort) {
            'nama_asc'  => $query->orderBy('nama_barang', 'asc'),
            'nama_desc' => $query->orderBy('nama_barang', 'desc'),
            'stok_asc'  => $query->orderBy('stok', 'asc'),
            'stok_desc' => $query->orderBy('stok', 'desc'),
            'terlama'   => $query->orderBy('id', 'asc'),
            default     => $query->orderBy('id', 'desc'), // 'terbaru'
        };

        $barang   = $query->paginate(15)->withQueryString();
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('laporan.stok', compact('barang', 'kategori', 'kategori_id', 'status', 'search', 'sort'));
    }

    /**
     * Export laporan transaksi ke CSV.
     */
    public function exportTransaksiCsv(Request $request)
    {
        $dari_tanggal   = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $tipe_transaksi = $request->input('tipe_transaksi', 'semua');
        $search         = $request->input('search');
        $sort           = $request->input('sort', 'tanggal_desc');

        $data = [];

        if ($tipe_transaksi === 'masuk' || $tipe_transaksi === 'semua') {
            $queryMasuk = BarangMasuk::with(['barang', 'user'])
                ->whereDate('tanggal', '>=', $dari_tanggal)
                ->whereDate('tanggal', '<=', $sampai_tanggal);

            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if ($search) {
                $queryMasuk->where(function ($q) use ($search, $like) {
                    $q->whereHas('barang', function ($b) use ($search, $like) {
                        $b->where('nama_barang', $like, '%' . $search . '%');
                    })->orWhere('sumber', $like, '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search, $like) {
                        $u->where('name', $like, '%' . $search . '%');
                    });
                });
            }

            $masuk = $queryMasuk->get()->map(function ($item) {
                return [
                    'id'          => $item->id,
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
            $queryKeluar = BarangKeluar::with(['barang', 'user', 'details.barangMasuk'])
                ->whereDate('tanggal', '>=', $dari_tanggal)
                ->whereDate('tanggal', '<=', $sampai_tanggal);

            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            if ($search) {
                $queryKeluar->where(function ($q) use ($search, $like) {
                    $q->whereHas('barang', function ($b) use ($search, $like) {
                        $b->where('nama_barang', $like, '%' . $search . '%');
                    })->orWhere('tujuan', $like, '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search, $like) {
                        $u->where('name', $like, '%' . $search . '%');
                    });
                });
            }

            $keluar = $queryKeluar->get()->map(function ($item) {
                $fifoBreakdown = $item->details->map(function ($d) {
                    return "Lot #{$d->barang_masuk_id} ({$d->jumlah_diambil} unit)";
                })->implode('; ');

                return [
                    'id'          => $item->id,
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

        usort($data, function ($a, $b) use ($sort) {
            $timeA = strtotime($a['tanggal']);
            $timeB = strtotime($b['tanggal']);

            if ($sort === 'tanggal_asc') {
                return $timeA === $timeB ? ($a['id'] <=> $b['id']) : ($timeA <=> $timeB);
            }

            return $timeA === $timeB ? ($b['id'] <=> $a['id']) : ($timeB <=> $timeA);
        });

        $filename = 'laporan-transaksi-' . $dari_tanggal . '-to-' . $sampai_tanggal . '.csv';

        return new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Tanggal', 'Tipe', 'Nama Barang', 'Jumlah Unit', 'Keterangan / Tujuan / Sumber', 'Petugas Input', 'Alokasi Lot FIFO']);

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
     * Export laporan posisi stok ke CSV.
     */
    public function exportStokCsv(Request $request)
    {
        $kategori_id = $request->input('kategori_id');
        $status      = $request->input('status');
        $search      = $request->input('search');
        $sort        = $request->input('sort', 'terbaru');

        $query = Barang::with('kategori');

        if ($search) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('nama_barang', $like, '%' . $search . '%')
                  ->orWhere('lokasi', $like, '%' . $search . '%');
            });
        }

        if ($kategori_id) {
            $query->where('kategori_id', $kategori_id);
        }

        if ($status === 'kurang') {
            $query->whereRaw('stok < stok_minimum');
        } elseif ($status === 'normal') {
            $query->whereRaw('stok >= stok_minimum');
        }

        match ($sort) {
            'nama_asc'  => $query->orderBy('nama_barang', 'asc'),
            'nama_desc' => $query->orderBy('nama_barang', 'desc'),
            'stok_asc'  => $query->orderBy('stok', 'asc'),
            'stok_desc' => $query->orderBy('stok', 'desc'),
            'terlama'   => $query->orderBy('id', 'asc'),
            default     => $query->orderBy('id', 'desc'), // 'terbaru'
        };

        $barang = $query->get();
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
