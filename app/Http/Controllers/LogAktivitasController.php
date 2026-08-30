<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $user_id        = $request->input('user_id');
        $search         = $request->input('search');
        $sort           = $request->input('sort', 'terbaru');
        $dari_tanggal   = $request->input('dari_tanggal', now()->subDays(30)->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));

        $query = LogAktivitas::with('user')
            ->whereBetween('waktu', [$dari_tanggal . ' 00:00:00', $sampai_tanggal . ' 23:59:59']);

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        if ($search) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where('aktivitas', $like, '%' . $search . '%');
        }

        if ($sort === 'terlama') {
            $query->orderBy('waktu', 'asc')->orderBy('id', 'asc');
        } else {
            $query->orderBy('waktu', 'desc')->orderBy('id', 'desc');
        }

        $logs = $query->paginate(50)->withQueryString();
        $users = \App\Models\User::orderBy('name', 'asc')->get();

        return view('log-aktivitas.index', compact('logs', 'users', 'user_id', 'dari_tanggal', 'sampai_tanggal', 'search', 'sort'));
    }
}
