<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with("kategori")->get();
        return view("barang.index", compact("barang"));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view("barang.create", compact("kategori"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "nama_barang" => "required",
            "kategori_id" => "required",
        ]);

        Barang::create($request->all());

        return redirect("/barang");
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
        $barang->update($request->all());

        return redirect("/barang");
    }

    public function destroy($id)
    {
        Barang::destroy($id);
        return redirect("/barang");
    }
}
