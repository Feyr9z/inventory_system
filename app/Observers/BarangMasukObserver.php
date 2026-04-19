<?php

namespace App\Observers;

use App\Models\BarangMasuk;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class BarangMasukObserver
{
    public function created(BarangMasuk $masuk): void
    {
        $barang_nama = $masuk->barang?->nama_barang ?? 'Unknown';
        $this->logActivity("Input barang masuk: {$barang_nama} ({$masuk->jumlah} unit)");
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
