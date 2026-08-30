<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockOpname;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function __construct(
        protected FifoService $fifoService
    ) {}

    public function create()
    {
        $barang = Barang::all();
        return view("transaksi.opname", compact("barang"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "barang_id"  => "required|exists:barang,id",
            "stok_fisik" => "required|integer|min:0",
            "tanggal"    => "required|date",
        ]);

        DB::transaction(function () use ($validated) {
            $barang = Barang::findOrFail($validated["barang_id"]);
            $stokFisik = (int) $validated["stok_fisik"];

            // 1. Rekonsiliasi lot FIFO dan update stok agregat (PRD §7)
            $selisih = $this->fifoService->adjustLotsForStockOpname(
                $barang,
                $stokFisik,
                $validated["tanggal"],
                Auth::id()
            );

            // 2. Simpan catatan riwayat audit Stock Opname
            StockOpname::create([
                "barang_id"  => $validated["barang_id"],
                "stok_fisik" => $stokFisik,
                "selisih"    => $selisih,
                "tanggal"    => $validated["tanggal"],
            ]);
        });

        return redirect()
            ->route("inventory.transaksi.opname.create")
            ->with("success", "Stock opname berhasil dicatat dan lot FIFO telah diselaraskan");
    }

    public function history(Request $request)
    {
        $query = StockOpname::with('barang');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('barang', function ($b) use ($search) {
                $b->where('nama_barang', 'ilike', '%' . $search . '%');
            });
        }

        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal', '>=', $request->input('dari_tanggal'));
        }

        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal', '<=', $request->input('sampai_tanggal'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'surplus') {
                $query->where('selisih', '>', 0);
            } elseif ($status === 'defisit') {
                $query->where('selisih', '<', 0);
            } elseif ($status === 'sesuai') {
                $query->where('selisih', '=', 0);
            }
        }

        $sort = $request->input('sort', 'terbaru');
        match ($sort) {
            'terlama'      => $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc'),
            'selisih_desc' => $query->orderBy('selisih', 'desc'),
            'selisih_asc'  => $query->orderBy('selisih', 'asc'),
            default        => $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc'), // 'terbaru'
        };

        $opname = $query->paginate(20)->withQueryString();

        return view('transaksi.opname-history', compact('opname', 'sort'));
    }
}
