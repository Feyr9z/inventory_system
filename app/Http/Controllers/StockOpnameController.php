<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "barang_id" => "required",
            "stok_fisik" => "required|integer",
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        $selisih = $request->stok_fisik - $barang->stok;

        // update stok
        $barang->stok = $request->stok_fisik;
        $barang->save();

        StockOpname::create([
            "barang_id" => $request->barang_id,
            "stok_fisik" => $request->stok_fisik,
            "selisih" => $selisih,
            "tanggal" => now(),
        ]);

        return back();
    }
}
