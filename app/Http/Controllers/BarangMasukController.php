<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function create()
    {
        $barang = Barang::all();
        return view("transaksi.masuk", compact("barang"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "barang_id" => "required|exists:barang,id",
            "jumlah"    => "required|integer|min:1",
            "tanggal"   => "required|date",
            "sumber"    => "required|string|max:255",
        ]);

        DB::transaction(function () use ($validated) {
            $barang = Barang::findOrFail($validated["barang_id"]);

            // Tambah stok agregat
            $barang->stok += $validated["jumlah"];
            $barang->save();

            // Simpan record transaksi
            BarangMasuk::create($validated);
        });

        return redirect()
            ->route("inventory.transaksi.masuk.create")
            ->with("success", "Barang masuk berhasil dicatat");
    }
}
