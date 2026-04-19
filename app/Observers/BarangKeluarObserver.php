<?php

namespace App\Observers;

use App\Models\BarangKeluar;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class BarangKeluarObserver
{
    public function created(BarangKeluar $keluar): void
    {
        $barang_nama = $keluar->barang?->nama_barang ?? 'Unknown';
        $this->logActivity("Input barang keluar: {$barang_nama} ({$keluar->jumlah} unit)");
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
