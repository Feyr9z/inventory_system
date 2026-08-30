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
        $opname = StockOpname::with('barang')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('transaksi.opname-history', compact('opname'));
    }
}
