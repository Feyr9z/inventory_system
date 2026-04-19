@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2 style="color: #2c3e50; margin: 0;">Kelola User</h2>
    <a href="{{ route('inventory.user.create') }}" class="btn btn-success">+ Tambah User</a>
</div>

@if ($users->isEmpty())
    <div class="card">
        <p>Belum ada user. <a href="{{ route('inventory.user.create') }}">Buat user baru</a></p>
    </div>
@else
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span style="background-color: #3498db; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.user.edit', $user->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                        @if ($user->id !== auth()->id())
                            <form action="{{ route('inventory.user.destroy', $user->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
