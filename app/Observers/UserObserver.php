<?php

namespace App\Observers;

use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user): void
    {
        $this->logActivity("Tambah user: {$user->name} ({$user->role})");
    }

    public function updated(User $user): void
    {
        $this->logActivity("Update user: {$user->name}");
    }

    public function deleted(User $user): void
    {
        $this->logActivity("Hapus user: {$user->name}");
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
