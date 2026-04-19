@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Daftar Kategori</h2>
    <a href="{{ route('inventory.kategori.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
</div>

@if ($kategori->isEmpty())
    <div class="alert alert-info" role="alert">
        <strong>Belum ada kategori.</strong> <a href="{{ route('inventory.kategori.create') }}" class="alert-link">Buat kategori baru</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Barang</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategori as $item)
                    <tr>
                        <td><small class="text-muted">#{{ $item->id }}</small></td>
                        <td><strong>{{ $item->nama_kategori }}</strong></td>
                        <td><span class="badge bg-info">{{ $item->barang_count }}</span></td>
                        <td>
                            <a href="{{ route('inventory.kategori.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('inventory.kategori.destroy', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
