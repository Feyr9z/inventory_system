<?php

namespace App\Observers;

use App\Models\StockOpname;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class StockOpnameObserver
{
    public function created(StockOpname $opname): void
    {
        $barang_nama = $opname->barang?->nama_barang ?? 'Unknown';
        $this->logActivity("Stock opname: {$barang_nama} (Fisik: {$opname->stok_fisik}, Sistem: {$opname->barang?->stok})");
    }

    private function logActivity(string $aktivitas): void
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => $aktivitas,
                'waktu' => now(),
            ]);
        }
    }
}
