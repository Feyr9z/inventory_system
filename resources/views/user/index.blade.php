@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Kelola Pengguna</h3>
        <p class="text-muted small mb-0">Kelola akun pengguna dan hak akses peran (Admin, Staff, Management)</p>
    </div>
    <a href="{{ route('inventory.user.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 fw-semibold">
        <i class="bi bi-person-plus-fill"></i> Tambah User
    </a>
</div>

<div class="card-elevated overflow-hidden">
    @if ($users->isEmpty())
        <div class="p-5 text-center text-muted">
            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Belum Ada Pengguna</h5>
            <p class="small mb-3">Buat pengguna baru untuk memberikan akses ke aplikasi.</p>
            <a href="{{ route('inventory.user.create') }}" class="btn btn-sm btn-primary">
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
                                @else
                                    <span class="badge badge-subtle-info d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-briefcase-fill"></i> Management
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('inventory.user.edit', $user->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('inventory.user.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus akun user ini?')">
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
    @endif
</div>
@endsection
