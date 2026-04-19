<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with("kategori");
        
        // Search by nama_barang
        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        $barang = $query->paginate(15);
        return view("barang.index", compact("barang"));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view("barang.create", compact("kategori"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "nama_barang" => "required|string|max:255",
            "kategori_id" => "required|exists:kategori,id",
            "stok_minimum" => "required|integer|min:0",
            "lokasi" => "nullable|string|max:255",
        ]);

        Barang::create($validated);

        return redirect()
            ->route("inventory.barang.index")
            ->with("success", "Barang berhasil ditambahkan");
    }

    public function show($id)
    {
        $barang = Barang::with("kategori", "barangMasuk", "barangKeluar")->findOrFail($id);
        return view("barang.show", compact("barang"));
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategori = Kategori::all();

        return view("barang.edit", compact("barang", "kategori"));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            "nama_barang" => "required|string|max:255",
            "kategori_id" => "required|exists:kategori,id",
            "stok_minimum" => "required|integer|min:0",
            "lokasi" => "nullable|string|max:255",
        ]);

        $barang->update($validated);

        return redirect()
            ->route("inventory.barang.index")
            ->with("success", "Barang berhasil diperbarui");
    }

    public function destroy($id)
    {
        Barang::destroy($id);
        return redirect()
            ->route("inventory.barang.index")
            ->with("success", "Barang berhasil dihapus");
    }
}
