@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Daftar Kategori</h3>
        <p class="text-muted small mb-0">Kelola pengelompokan jenis barang inventaris</p>
    </div>
    <a href="{{ route('inventory.kategori.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 fw-semibold">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </a>
</div>

<div class="card-elevated overflow-hidden">
    @if ($kategori->isEmpty())
        <div class="p-5 text-center text-muted">
            <i class="bi bi-tags fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Belum Ada Kategori</h5>
            <p class="small mb-3">Buat kategori baru untuk mengelompokkan barang kamu.</p>
            <a href="{{ route('inventory.kategori.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Buat Kategori Baru
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Barang Terkait</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategori as $item)
                        <tr>
                            <td class="ps-4"><span class="text-muted fw-semibold small">#{{ $item->id }}</span></td>
                            <td>
                                <span class="fw-bold text-dark d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-tag-fill text-primary"></i> {{ $item->nama_kategori }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-subtle-primary">
                                    {{ $item->barang_count }} Barang
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('inventory.kategori.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('inventory.kategori.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
