@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Kelola Pengguna</h3>
        <p class="text-muted small mb-0">Kelola akun pengguna dan hak akses peran (Admin, Staff, Management)</p>
    </div>
    <a href="{{ route('inventory.user.create') }}" class="btn-app-primary">
        <i class="bi bi-person-plus-fill"></i> Tambah User
    </a>
</div>

<!-- Search & Filter Card -->
<div class="card-elevated p-3 mb-4">
    <form action="{{ route('inventory.user.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-lg-5 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama atau email user..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <select name="role" class="form-select">
                <option value="">-- Semua Role --</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->value }}" {{ request('role') === $r->value ? 'selected' : '' }}>
                        {{ $r->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <select name="sort" class="form-select">
                <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                <option value="nama_asc" {{ request('sort') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                <option value="nama_desc" {{ request('sort') === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6 d-flex gap-2">
            <button type="submit" class="btn btn-app-primary flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-filter"></i> Filter
            </button>
            @if (request()->hasAny(['search', 'role', 'sort']))
                <a href="{{ route('inventory.user.index') }}" class="btn btn-app-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card-elevated overflow-hidden">
    @if ($users->isEmpty())
        <div class="p-5 text-center text-muted">
            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Belum Ada Pengguna</h5>
            <p class="small mb-3">Buat pengguna baru untuk memberikan akses ke aplikasi.</p>
            <a href="{{ route('inventory.user.create') }}" class="btn-app-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Buat User Baru
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama User</th>
                        <th>Email</th>
                        <th>Peran (Role)</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="ps-4"><span class="text-muted fw-semibold small">#{{ $user->id }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td><span class="text-secondary small">{{ $user->email }}</span></td>
                            <td>
                                @if ($user->role === 'admin')
                                    <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-shield-lock-fill"></i> Admin
                                    </span>
                                @elseif ($user->role === 'staff')
                                    <span class="badge badge-subtle-primary d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-person-badge-fill"></i> Staff
                                    </span>
                                @elseif ($user->role === 'kepala_gudang')
                                    <span class="badge badge-subtle-info d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-person-gear"></i> Kepala Gudang
                                    </span>
                                @else
                                    <span class="badge badge-subtle-secondary d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-briefcase-fill"></i> Management
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1.5">
                                    <a href="{{ route('inventory.user.edit', $user->id) }}" class="btn-action-edit" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('inventory.user.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus akun user ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection
