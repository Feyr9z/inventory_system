<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function create()
    {
        $barang = Barang::all();
        return view("transaksi.opname", compact("barang"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "barang_id" => "required|exists:barang,id",
            "stok_fisik" => "required|integer|min:0",
            "tanggal" => "required|date",
        ]);

        $barang = Barang::findOrFail($validated["barang_id"]);

        $selisih = $validated["stok_fisik"] - $barang->stok;

        // update stok
        $barang->stok = $validated["stok_fisik"];
        $barang->save();

        StockOpname::create([
            "barang_id" => $validated["barang_id"],
            "stok_fisik" => $validated["stok_fisik"],
            "selisih" => $selisih,
            "tanggal" => $validated["tanggal"],
        ]);

        return redirect()
            ->route("inventory.transaksi.opname.create")
            ->with("success", "Stock opname berhasil dicatat");
    }
}
