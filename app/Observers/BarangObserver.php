<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class BarangObserver
{
    public function created(Barang $barang): void
    {
        $this->logActivity("Tambah barang: {$barang->nama_barang}");
    }

    public function updated(Barang $barang): void
    {
        $this->logActivity("Update barang: {$barang->nama_barang}");
    }

    public function deleted(Barang $barang): void
    {
        $this->logActivity("Hapus barang: {$barang->nama_barang}");
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
