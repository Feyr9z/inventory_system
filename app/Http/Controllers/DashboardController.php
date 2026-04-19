<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $total = Barang::count();
        $stok = Barang::sum("stok");
        $barang_minimum = Barang::whereRaw('stok < stok_minimum')->count();

        $data = [
            'total' => $total,
            'stok' => $stok,
            'barang_minimum' => $barang_minimum,
            'role' => $user->role,
        ];

        if ($user->role === 'admin') {
            $data['barang_masuk_bulan_ini'] = BarangMasuk::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah');
            $data['barang_keluar_bulan_ini'] = BarangKeluar::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah');
            $data['opname_bulan_ini'] = StockOpname::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count();
        } elseif ($user->role === 'staff') {
            $data['transaksi_bulan_ini'] = BarangMasuk::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count() + BarangKeluar::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count();
        }

        return view("dashboard", $data);
    }
}
