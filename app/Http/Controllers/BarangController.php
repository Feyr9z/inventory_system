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

        // Search by nama_barang atau lokasi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ilike', '%' . $search . '%')
                  ->orWhere('lokasi', 'ilike', '%' . $search . '%');
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->input('kategori'));
        }

        // Filter by status stok
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'kurang') {
                $query->whereRaw('stok < stok_minimum');
            } elseif ($status === 'normal') {
                $query->whereRaw('stok >= stok_minimum');
            }
        }

        // Sorting (Default: Terbaru di atas / id DESC)
        $sort = $request->input('sort', 'terbaru');
        match ($sort) {
            'terlama'   => $query->orderBy('id', 'asc'),
            'nama_asc'  => $query->orderBy('nama_barang', 'asc'),
            'nama_desc' => $query->orderBy('nama_barang', 'desc'),
            'stok_desc' => $query->orderBy('stok', 'desc'),
            'stok_asc'  => $query->orderBy('stok', 'asc'),
            default     => $query->orderBy('id', 'desc'), // 'terbaru'
        };

        $barang = $query->paginate(15)->withQueryString();
        $kategoriList = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view("barang.index", compact("barang", "kategoriList", "sort"));
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
        $barang = Barang::with([
            'kategori',
            'barangMasuk' => function ($q) {
                $q->with('user')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
            },
            'barangKeluar' => function ($q) {
                $q->with(['user', 'details.barangMasuk'])->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
            }
        ])->findOrFail($id);

        return view('barang.show', compact('barang'));
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
