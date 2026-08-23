@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Log Aktivitas System</h3>
        <p class="text-muted small mb-0">Jejak audit dan rekaman seluruh aktivitas pengguna di sistem inventaris</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card-elevated p-4 mb-4">
    <h6 class="fw-bold text-dark mb-3">Filter Log Aktivitas</h6>
    <form action="{{ route('inventory.log-aktivitas') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label for="dari_tanggal" class="form-label fw-semibold small text-secondary">Dari Tanggal</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar text-muted"></i></span>
                <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control border-start-0 ps-0" value="{{ $dari_tanggal }}">
            </div>
        </div>

        <div class="col-md-3">
            <label for="sampai_tanggal" class="form-label fw-semibold small text-secondary">Sampai Tanggal</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check text-muted"></i></span>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control border-start-0 ps-0" value="{{ $sampai_tanggal }}">
            </div>
        </div>

        <div class="col-md-3">
            <label for="user_id" class="form-label fw-semibold small text-secondary">Pengguna</label>
            <select id="user_id" name="user_id" class="form-select">
                <option value="">-- Semua User --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ ucfirst($user->role) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-search"></i> Filter Log
            </button>
        </div>
    </form>
</div>

<div class="card-elevated overflow-hidden mb-4">
    @if ($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Pengguna</th>
                        <th class="pe-4">Aktivitas Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <span class="badge badge-subtle-secondary d-inline-flex align-items-center gap-1 font-monospace">
                                    <i class="bi bi-clock me-1"></i>{{ $log->waktu->format('d/m/Y H:i:s') }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block lh-1 mb-1">{{ $log->user->name ?? 'System' }}</span>
                                        <span class="badge badge-subtle-primary fs-8 text-capitalize">{{ $log->user->role ?? 'user' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="pe-4">
                                <span class="text-dark small fw-medium">{{ $log->aktivitas }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="p-5 text-center text-muted">
            <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Tidak Ada Log Aktivitas</h5>
            <p class="small mb-0">Tidak ditemukan log aktivitas pengguna untuk kriteria filter periode ini.</p>
        </div>
    @endif
</div>
@endsection
