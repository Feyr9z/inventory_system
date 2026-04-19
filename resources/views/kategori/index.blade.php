@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2 style="color: #2c3e50; margin: 0;">Kategori</h2>
    <a href="{{ route('inventory.kategori.create') }}" class="btn btn-success">+ Tambah Kategori</a>
</div>

@if ($kategori->isEmpty())
    <div class="card">
        <p>Belum ada kategori. <a href="{{ route('inventory.kategori.create') }}">Buat kategori baru</a></p>
    </div>
@else
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Jumlah Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kategori as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->nama_kategori }}</td>
                    <td>{{ $item->barang_count }}</td>
                    <td>
                        <a href="{{ route('inventory.kategori.edit', $item->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                        <form action="{{ route('inventory.kategori.destroy', $item->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
