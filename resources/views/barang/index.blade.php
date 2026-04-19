@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Daftar Barang</h2>
    @if (auth()->user()->role === 'admin')
        <a href="{{ route('inventory.barang.create') }}" class="btn btn-primary">+ Tambah Barang</a>
    @endif
</div>

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        ⚠️ {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('inventory.barang.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach (\App\Models\Kategori::all() as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">🔍 Cari</button>
            </div>
        </form>
    </div>
</div>

@if ($barang->isEmpty())
    <div class="alert alert-info" role="alert">
        <strong>Belum ada barang.</strong> <a href="{{ route('inventory.barang.create') }}" class="alert-link">Tambah barang baru</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Minimum</th>
                    <th>Lokasi</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $item)
                    <tr>
                        <td><small class="text-muted">#{{ $item->id }}</small></td>
                        <td><strong>{{ $item->nama_barang }}</strong></td>
                        <td><span class="badge bg-info">{{ $item->kategori->nama_kategori ?? '-' }}</span></td>
                        <td>
                            @if ($item->stok < $item->stok_minimum)
                                <span class="stock-alert">{{ $item->stok }}</span>
                                <span class="badge bg-danger">RENDAH</span>
                            @else
                                <strong>{{ $item->stok }}</strong>
                            @endif
                        </td>
                        <td>{{ $item->stok_minimum }}</td>
                        <td><small>{{ $item->lokasi ?? '-' }}</small></td>
                        <td>
                            <a href="{{ route('inventory.barang.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @if (auth()->user()->role === 'admin')
                                <form action="{{ route('inventory.barang.destroy', $item->id) }}" method="POST" style="display: inline;">
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

    @if ($barang->hasPages())
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                {{ $barang->links() }}
            </ul>
        </nav>
    @endif
@endif
@endsection
