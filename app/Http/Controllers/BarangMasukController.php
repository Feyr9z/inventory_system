<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "barang_id" => "required",
            "jumlah" => "required|integer|min:1",
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // tambah stok
        $barang->stok += $request->jumlah;
        $barang->save();

        // simpan transaksi
        BarangMasuk::create($request->all());

        return back();
    }
}
