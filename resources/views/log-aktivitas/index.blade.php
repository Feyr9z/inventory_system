@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<h2 class="page-title mb-4">Log Aktivitas</h2>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Filter Log</h5>
        <form action="{{ route('inventory.log-aktivitas') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control" value="{{ $dari_tanggal }}">
            </div>

            <div class="col-md-3">
                <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control" value="{{ $sampai_tanggal }}">
            </div>

            <div class="col-md-3">
                <label for="user_id" class="form-label">User</label>
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
                <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
            </div>
        </form>
    </div>
</div>

@if ($logs->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td><small>{{ $log->waktu->format('d-m-Y H:i:s') }}</small></td>
                        <td>
                            <strong>{{ $log->user->name }}</strong>
                            <br>
                            <span class="badge bg-secondary">{{ ucfirst($log->user->role) }}</span>
                        </td>
                        <td>{{ $log->aktivitas }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            {{ $logs->links() }}
        </ul>
    </nav>
@else
    <div class="alert alert-info" role="alert">
        Tidak ada log aktivitas untuk periode ini.
    </div>
@endif
@endsection
