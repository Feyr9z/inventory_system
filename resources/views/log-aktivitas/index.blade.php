@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Log Aktivitas</h2>

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem;">Filter Log</h3>
    <form action="{{ route('inventory.log-aktivitas') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="dari_tanggal">Dari Tanggal</label>
            <input type="date" id="dari_tanggal" name="dari_tanggal" value="{{ $dari_tanggal }}">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="sampai_tanggal">Sampai Tanggal</label>
            <input type="date" id="sampai_tanggal" name="sampai_tanggal" value="{{ $sampai_tanggal }}">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="user_id">User</label>
            <select id="user_id" name="user_id">
                <option value="">-- Semua User --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ ucfirst($user->role) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-success" style="width: 100%;">Filter</button>
        </div>
    </form>
</div>

@if ($logs->count() > 0)
    <table>
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
                    <td>{{ $log->waktu->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $log->user->name }} <span style="color: #7f8c8d; font-size: 0.85rem;">({{ ucfirst($log->user->role) }})</span></td>
                    <td>{{ $log->aktivitas }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 2rem;">
        {{ $logs->links() }}
    </div>
@else
    <div class="card">
        <p>Tidak ada log aktivitas untuk periode ini.</p>
    </div>
@endif
@endsection
