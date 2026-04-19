@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2 style="color: #2c3e50; margin: 0;">Daftar Barang</h2>
    <a href="{{ route('inventory.barang.create') }}" class="btn btn-success">+ Tambah Barang</a>
</div>

@if ($barang->isEmpty())
    <div class="card">
        <p>Belum ada barang. <a href="{{ route('inventory.barang.create') }}">Tambah barang baru</a></p>
    </div>
@else
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Stok Minimum</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>
                        <strong>{{ $item->stok }}</strong>
                        @if ($item->stok < $item->stok_minimum)
                            <span style="color: #e74c3c;"> (⚠️ Dibawah Minimum)</span>
                        @endif
                    </td>
                    <td>{{ $item->stok_minimum }}</td>
                    <td>{{ $item->lokasi ?? '-' }}</td>
                    <td>
                        <a href="{{ route('inventory.barang.edit', $item->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                        <form action="{{ route('inventory.barang.destroy', $item->id) }}" method="POST" style="display: inline;">
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
