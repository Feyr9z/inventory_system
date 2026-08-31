<?php

namespace App\Http\Controllers;

use App\Enums\Role;
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

        $data = [
            'role'           => $user->role,
            'total'          => Barang::count(),
            'stok'           => Barang::sum('stok'),
            'barang_minimum' => Barang::whereRaw('stok < stok_minimum')->count(),
        ];

        // Statistik bulanan berdasarkan role
        $roleValue = $user->role;

        if (in_array($roleValue, [
            Role::Admin->value,
            Role::KepalaGudang->value,
            Role::Management->value,
        ])) {
            $data['barang_masuk_bulan_ini'] = BarangMasuk::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah');

            $data['barang_keluar_bulan_ini'] = BarangKeluar::where('status', 'disetujui')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah');

            $data['opname_bulan_ini'] = StockOpname::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count();

            $data['pending_approvals_count'] = BarangKeluar::pending()->count();
        } elseif ($roleValue === Role::Staff->value) {
            $data['barang_masuk_bulan_ini'] = BarangMasuk::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah');

            $data['barang_keluar_bulan_ini'] = BarangKeluar::where('status', 'disetujui')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah');

            $data['pending_approvals_count'] = 0;
        }

        return view('dashboard', $data);
    }
}
