@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Kelola User</h2>
    <a href="{{ route('inventory.user.create') }}" class="btn btn-primary">+ Tambah User</a>
</div>

@if ($users->isEmpty())
    <div class="alert alert-info" role="alert">
        <strong>Belum ada user.</strong> <a href="{{ route('inventory.user.create') }}" class="alert-link">Buat user baru</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td><small class="text-muted">#{{ $user->id }}</small></td>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                            @elseif ($user->role === 'staff')
                                <span class="badge bg-primary">Staff</span>
                            @else
                                <span class="badge bg-info">Management</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('inventory.user.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('inventory.user.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
