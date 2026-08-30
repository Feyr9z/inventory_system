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
    <h6 class="fw-bold text-dark mb-3">Filter Kategori & Status</h6>
    <form action="{{ route('inventory.laporan.stok') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label for="kategori_id" class="form-label fw-semibold small text-secondary">Kategori</label>
            <select id="kategori_id" name="kategori_id" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ $kategori_id == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="status" class="form-label fw-semibold small text-secondary">Status Stok</label>
            <select id="status" name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="normal" {{ $status == 'normal' ? 'selected' : '' }}>Normal (Aman)</option>
                <option value="kurang" {{ $status == 'kurang' ? 'selected' : '' }}>Kurang (Perlu Restock)</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-filter"></i> Filter
            </button>
            @if ($barang->count() > 0)
                <a href="{{ route('inventory.laporan.stok.export') }}?kategori_id={{ $kategori_id }}&status={{ $status }}" class="btn btn-success fw-semibold d-flex align-items-center gap-1" title="Download CSV">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            @endif
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
