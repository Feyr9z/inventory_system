<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class BarangKeluarController extends Controller
{
    public function __construct(
        protected FifoService $fifoService
    ) {}

    public function create()
    {
        $barang = Barang::all();
        return view("transaksi.keluar", compact("barang"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "barang_id" => "required|exists:barang,id",
            "jumlah"    => "required|integer|min:1",
            "tanggal"   => "required|date",
            "tujuan"    => "required|string|max:255",
        ]);

        try {
            $this->fifoService->processBarangKeluar($validated, Auth::id());
        } catch (InvalidArgumentException $e) {
            return back()
                ->withErrors(["jumlah" => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route("inventory.transaksi.keluar.create")
            ->with("success", "Barang keluar berhasil dicatat dengan alokasi FIFO");
    }
}
