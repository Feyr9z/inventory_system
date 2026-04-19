<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

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
            "jumlah" => "required|integer|min:1",
            "tanggal" => "required|date",
            "tujuan" => "required|string|max:255",
        ]);

        $barang = Barang::findOrFail($validated["barang_id"]);

        // Validasi stok
        if ($validated["jumlah"] > $barang->stok) {
            return back()
                ->withErrors(["jumlah" => "Stok tidak cukup. Stok tersedia: {$barang->stok}"])
                ->withInput();
        }

        // kurangi stok
        $barang->stok -= $validated["jumlah"];
        $barang->save();

        BarangKeluar::create($validated);

        return redirect()
            ->route("inventory.transaksi.keluar.create")
            ->with("success", "Barang keluar berhasil dicatat");
    }
}

