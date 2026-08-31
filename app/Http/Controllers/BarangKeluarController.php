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

        $user = Auth::user();

        try {
            if ($user->role === 'staff') {
                $this->fifoService->submitPengajuan($validated, $user->id);
                $pesan = "Permohonan pengeluaran barang berhasil dicatat dan telah masuk ke antrean pemeriksaan Kepala Gudang.";
            } else {
                $this->fifoService->processBarangKeluar($validated, $user->id);
                $pesan = "Barang keluar berhasil dicatat dan alokasi FIFO telah diproses.";
            }
        } catch (InvalidArgumentException $e) {
            return back()
                ->withErrors(["jumlah" => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route("inventory.transaksi.keluar.create")
            ->with("success", $pesan);
    }
}
