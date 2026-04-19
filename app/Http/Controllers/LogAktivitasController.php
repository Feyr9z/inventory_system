<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->input('user_id', null);
        // Default: last 7 days instead of start of month
        $dari_tanggal = $request->input('dari_tanggal', now()->subDays(7)->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));

        $logs = LogAktivitas::with('user')
            ->whereBetween('waktu', [$dari_tanggal . ' 00:00:00', $sampai_tanggal . ' 23:59:59']);

        if ($user_id) {
            $logs = $logs->where('user_id', $user_id);
        }

        $logs = $logs->orderBy('waktu', 'desc')->paginate(50);

        $users = \App\Models\User::all();

        return view('log-aktivitas.index', compact('logs', 'users', 'user_id', 'dari_tanggal', 'sampai_tanggal'));
    }
}
