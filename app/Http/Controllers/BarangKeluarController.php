<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "barang_id" => "required",
            "jumlah" => "required|integer|min:1",
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($request->jumlah > $barang->stok) {
            return back()->with("error", "Stok tidak cukup");
        }

        // kurangi stok
        $barang->stok -= $request->jumlah;
        $barang->save();

        BarangKeluar::create($request->all());

        return back();
    }
}
