<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $barang = Barang::where('id', $validated["barang_id"])
                ->lockForUpdate()
                ->firstOrFail();

            // Tambah stok agregat
            $barang->stok += (int) $validated["jumlah"];
            $barang->save();

            // Inisialisasi lot persediaan baru (sisa_jumlah = jumlah)
            BarangMasuk::create([
                "barang_id"   => $barang->id,
                "user_id"     => Auth::id(),
                "tanggal"     => $validated["tanggal"],
                "jumlah"      => (int) $validated["jumlah"],
                "sisa_jumlah" => (int) $validated["jumlah"],
                "sumber"      => $validated["sumber"],
            ]);
        });

        return redirect()
            ->route("inventory.transaksi.masuk.create")
            ->with("success", "Barang masuk berhasil dicatat");
    }
}
