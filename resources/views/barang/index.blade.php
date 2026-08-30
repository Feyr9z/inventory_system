@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Daftar Barang</h3>
        <p class="text-muted small mb-0">Kelola inventaris stok dan informasi barang</p>
    </div>
    @if (auth()->user()->role === 'admin')
        <a href="{{ route('inventory.barang.create') }}" class="btn-app-primary">
            <i class="bi bi-plus-lg"></i> Tambah Barang
        </a>
    @endif
</div>

@if (session('warning'))
    <div class="alert alert-warning alert-custom alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0"></i>
        <div>{{ session('warning') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Search & Filter Card -->
<div class="card-elevated p-3 mb-4">
    <form action="{{ route('inventory.barang.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama barang atau lokasi..." value="{{ request('search') }}">
            </div>
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
            <button type="submit" class="btn btn-outline-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card-elevated overflow-hidden">
    @if ($barang->isEmpty())
        <div class="p-5 text-center text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Belum Ada Barang</h5>
            <p class="small mb-3">Tidak ada data barang yang sesuai dengan pencarian kamu.</p>
            @if (in_array(auth()->user()->role, ['admin', 'kepala_gudang']))
                <a href="{{ route('inventory.barang.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Barang Baru
                </a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Unit</th>
                        <th>Stok Min.</th>
                        <th>Lokasi</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barang as $item)
                        <tr>
                            <td class="ps-4"><span class="text-muted fw-semibold small">#{{ $item->id }}</span></td>
                            <td>
                                <a href="{{ route('inventory.barang.show', $item->id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                    {{ $item->nama_barang }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-subtle-info">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $item->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($item->stok < $item->stok_minimum)
                                    <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $item->stok }} (Rendah)
                                    </span>
                                @else
                                    <span class="fw-semibold text-dark">{{ $item->stok }}</span>
                                @endif
                            </td>
                            <td><span class="text-secondary">{{ $item->stok_minimum }}</span></td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $item->lokasi ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1.5 align-items-center">
                                    <a href="{{ route('inventory.barang.show', $item->id) }}" class="btn-action-detail" title="Lihat Detail & Lot FIFO">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    @if (in_array(auth()->user()->role, ['admin', 'kepala_gudang']))
                                        <a href="{{ route('inventory.barang.edit', $item->id) }}" class="btn-action-edit" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if (auth()->user()->role === 'admin')
                                        <form action="{{ route('inventory.barang.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus barang ini?')">
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

        @if ($barang->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $barang->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection
