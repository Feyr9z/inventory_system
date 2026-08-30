<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
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

        // Pre-check stok sebelum masuk transaksi (untuk error response yang bersih)
        $barang = Barang::findOrFail($validated["barang_id"]);
        if ($validated["jumlah"] > $barang->stok) {
            return back()
                ->withErrors(["jumlah" => "Stok tidak cukup. Stok tersedia: {$barang->stok}"])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {
            $barang = Barang::findOrFail($validated["barang_id"]);

            // Kurangi stok agregat
            $barang->stok -= $validated["jumlah"];
            $barang->save();

            BarangKeluar::create($validated);
        });

        return redirect()
            ->route("inventory.transaksi.keluar.create")
            ->with("success", "Barang keluar berhasil dicatat");
    }
}
