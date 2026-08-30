@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Laporan Stok Barang</h3>
        <p class="text-muted small mb-0">Monitor posisi ketersediaan stok barang, sisa lot aktif FIFO, dan deteksi stok kritis</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card-elevated p-4 mb-4">
    <h6 class="fw-bold text-dark mb-3">Filter & Pencarian Posisi Stok</h6>
    <form action="{{ route('inventory.laporan.stok') }}" method="GET" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="search" class="form-label fw-semibold small text-secondary">Cari Barang / Lokasi</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="search" name="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama / lokasi..." value="{{ $search ?? '' }}">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <label for="kategori_id" class="form-label fw-semibold small text-secondary">Kategori</label>
            <select id="kategori_id" name="kategori_id" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ ($kategori_id ?? '') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6">
            <label for="status" class="form-label fw-semibold small text-secondary">Status Stok</label>
            <select id="status" name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="normal" {{ ($status ?? '') === 'normal' ? 'selected' : '' }}>Normal (Aman)</option>
                <option value="kurang" {{ ($status ?? '') === 'kurang' ? 'selected' : '' }}>Kurang (Perlu Restock)</option>
            </select>
        </div>
        <div class="col-lg-3 col-md-6">
            <label for="sort" class="form-label fw-semibold small text-secondary">Urutan Tampilan</label>
            <select id="sort" name="sort" class="form-select">
                <option value="terbaru" {{ ($sort ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                <option value="terlama" {{ ($sort ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                <option value="nama_asc" {{ ($sort ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                <option value="nama_desc" {{ ($sort ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                <option value="stok_desc" {{ ($sort ?? '') === 'stok_desc' ? 'selected' : '' }}>Stok Tertinggi</option>
                <option value="stok_asc" {{ ($sort ?? '') === 'stok_asc' ? 'selected' : '' }}>Stok Terendah</option>
            </select>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
            @if (request()->hasAny(['search', 'kategori_id', 'status', 'sort']))
                <a href="{{ route('inventory.laporan.stok') }}" class="btn btn-app-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            @endif
            @if ($barang->count() > 0)
                <a href="{{ route('inventory.laporan.stok.export') }}?kategori_id={{ $kategori_id }}&status={{ $status }}&search={{ urlencode($search ?? '') }}&sort={{ $sort ?? 'terbaru' }}" class="btn btn-success fw-semibold d-inline-flex align-items-center gap-1" title="Download CSV">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            @endif
            <button type="submit" class="btn btn-app-primary fw-semibold d-inline-flex align-items-center gap-1">
                <i class="bi bi-filter"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

@if ($barang->count() > 0)
    <div class="card-elevated overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-end">Stok Total</th>
                        <th class="text-end">Stok Min</th>
                        <th>Status Stok</th>
                        <th>Lot Aktif (FIFO)</th>
                        <th class="pe-4">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barang as $idx => $item)
                        <tr>
                            <td class="ps-4">
                                <span class="text-muted fw-semibold small">
                                    #{{ $barang->firstItem() + $idx }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('inventory.barang.show', $item->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                    {{ $item->nama_barang }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-subtle-info">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $item->kategori?->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold fs-6 {{ $item->stok < $item->stok_minimum ? 'text-danger' : 'text-dark' }}">
                                    {{ number_format($item->stok) }}
                                </span>
                            </td>
                            <td class="text-end"><span class="text-secondary">{{ number_format($item->stok_minimum) }}</span></td>
                            <td>
                                @if ($item->stok < $item->stok_minimum)
                                    <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Kurang (Restock)
                                    </span>
                                @else
                                    <span class="badge badge-subtle-success d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Normal (Aman)
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php $activeLotCount = $item->barangMasuk->count(); @endphp
                                @if ($activeLotCount > 0)
                                    <span class="badge badge-subtle-secondary d-inline-flex align-items-center gap-1" title="{{ $activeLotCount }} lot masuk masih memiliki sisa unit">
                                        <i class="bi bi-layers-fill text-primary"></i> {{ $activeLotCount }} Lot Aktif
                                    </span>
                                @else
                                    <span class="text-muted small">0 Lot</span>
                                @endif
                            </td>
                            <td class="pe-4"><span class="text-muted small">{{ $item->lokasi ?? '-' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($barang->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $barang->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@else
    <div class="card-elevated p-5 text-center text-muted mb-4">
        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
        <h5 class="fw-semibold text-dark">Tidak Ada Data Stok</h5>
        <p class="small mb-0">Tidak ditemukan barang yang sesuai dengan kriteria filter yang kamu pilih.</p>
    </div>
@endif
@endsection
