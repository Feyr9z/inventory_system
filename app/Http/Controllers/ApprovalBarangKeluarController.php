<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApprovalBarangKeluarController extends Controller
{
    public function __construct(
        protected FifoService $fifoService
    ) {}

    /**
     * Menampilkan daftar pengajuan barang keluar yang membutuhkan persetujuan
     * serta riwayat pengajuan yang telah diproses.
     */
    public function index(Request $request)
    {
        $tab    = $request->input('tab', 'pending');
        $search = $request->input('search');
        $status = $request->input('status');
        $sort   = $request->input('sort', 'terbaru');

        $like = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';

        // Hitung total pengajuan yang sedang pending untuk counter badge
        $countPending = BarangKeluar::pending()->count();

        $query = BarangKeluar::with(['barang.kategori', 'user', 'approver', 'details.barangMasuk']);

        if ($tab === 'pending') {
            $query->pending();
        } else {
            // Tab riwayat (disetujui, ditolak, atau semua non-pending)
            if ($status && in_array($status, ['disetujui', 'ditolak'])) {
                $query->where('status', $status);
            } else {
                $query->whereIn('status', ['disetujui', 'ditolak']);
            }
        }

        // Pencarian kata kunci multi-kolom
        if ($search) {
            $query->where(function ($q) use ($search, $like) {
                $q->whereHas('barang', function ($b) use ($search, $like) {
                    $b->where('nama_barang', $like, '%' . $search . '%');
                })->orWhere('tujuan', $like, '%' . $search . '%')
                  ->orWhereHas('user', function ($u) use ($search, $like) {
                    $u->where('name', $like, '%' . $search . '%');
                });
            });
        }

        // Filter tanggal jika ada
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->input('dari_tanggal'));
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->input('sampai_tanggal'));
        }

        // Sorting
        match ($sort) {
            'terlama'    => $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc'),
            'jumlah_desc'=> $query->orderBy('jumlah', 'desc'),
            'jumlah_asc' => $query->orderBy('jumlah', 'asc'),
            default      => $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc'), // 'terbaru'
        };

        $pengajuan = $query->paginate(15)->withQueryString();

        return view('transaksi.approval', compact(
            'pengajuan',
            'tab',
            'search',
            'status',
            'sort',
            'countPending'
        ));
    }

    /**
     * Menyetujui pengajuan pengeluaran barang dan mengeksekusi FIFO secara atomik.
     */
    public function approve(Request $request, $id)
    {
        try {
            $barangKeluar = $this->fifoService->approvePengajuan((int) $id, Auth::id());

            return redirect()
                ->route('inventory.transaksi.approval.index', ['tab' => 'pending'])
                ->with('success', "Pengajuan pengeluaran barang '{$barangKeluar->barang->nama_barang}' ({$barangKeluar->jumlah} unit) berhasil disetujui dan alokasi FIFO telah dieksekusi.");
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withErrors(['approval' => $e->getMessage()]);
        }
    }

    /**
     * Menolak pengajuan pengeluaran barang disertai catatan alasan penolakan.
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|min:3|max:500',
        ], [
            'catatan_penolakan.required' => 'Wajib mengisi alasan penolakan transaksi.',
            'catatan_penolakan.min'      => 'Alasan penolakan minimal berisi 3 karakter.',
        ]);

        try {
            $barangKeluar = $this->fifoService->rejectPengajuan((int) $id, Auth::id(), $validated['catatan_penolakan']);

            return redirect()
                ->route('inventory.transaksi.approval.index', ['tab' => 'pending'])
                ->with('success', "Pengajuan pengeluaran barang '{$barangKeluar->barang->nama_barang}' telah ditolak.");
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withErrors(['approval' => $e->getMessage()]);
        }
    }
}
