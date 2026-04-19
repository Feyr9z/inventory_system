<?php

namespace App\Observers;

use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class KategoriObserver
{
    public function created(Kategori $kategori): void
    {
        $this->logActivity("Tambah kategori: {$kategori->nama_kategori}");
    }

    public function updated(Kategori $kategori): void
    {
        $this->logActivity("Update kategori: {$kategori->nama_kategori}");
    }

    public function deleted(Kategori $kategori): void
    {
        $this->logActivity("Hapus kategori: {$kategori->nama_kategori}");
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
