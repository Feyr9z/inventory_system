@extends('layouts.app')

@section('title', 'History Stock Opname')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">History Stock Opname</h3>
        <p class="text-muted small mb-0">Riwayat hasil perhitungan fisik stok dan audit penyesuaian inventaris</p>
    </div>
    @if (in_array(auth()->user()->role, ['admin', 'kepala_gudang']))
        <a href="{{ route('inventory.transaksi.opname.create') }}" class="btn-app-primary d-inline-flex align-items-center gap-2 fw-semibold">
            <i class="bi bi-clipboard-check"></i> Stock Opname Baru
        </a>
    @endif
</div>

<!-- Filter Card -->
<div class="card-elevated p-3 mb-4">
    <form action="{{ route('inventory.transaksi.opname.history') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-lg-3 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama barang..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-6">
            <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}" placeholder="Dari Tanggal" title="Dari Tanggal">
        </div>
        <div class="col-lg-2 col-md-4">
            <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}" placeholder="Sampai Tanggal" title="Sampai Tanggal">
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="surplus" {{ request('status') === 'surplus' ? 'selected' : '' }}>Surplus (+)</option>
                <option value="defisit" {{ request('status') === 'defisit' ? 'selected' : '' }}>Defisit (-)</option>
                <option value="sesuai" {{ request('status') === 'sesuai' ? 'selected' : '' }}>Sesuai (0)</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="sort" class="form-select">
                <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru di Atas</option>
                <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama di Atas</option>
                <option value="selisih_desc" {{ request('sort') === 'selisih_desc' ? 'selected' : '' }}>Selisih Terbesar</option>
                <option value="selisih_asc" {{ request('sort') === 'selisih_asc' ? 'selected' : '' }}>Selisih Terkecil</option>
            </select>
        </div>
        <div class="col-lg-1 col-md-4 d-flex gap-1">
            <button type="submit" class="btn btn-app-primary w-100" title="Terapkan Filter">
                <i class="bi bi-filter"></i>
            </button>
            @if (request()->hasAny(['search', 'dari_tanggal', 'sampai_tanggal', 'status', 'sort']))
                <a href="{{ route('inventory.transaksi.opname.history') }}" class="btn btn-app-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card-elevated overflow-hidden mb-4">
    @if ($opname->count() > 0)
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Stok Fisik</th>
                        <th class="text-end">Selisih Unit</th>
                        <th class="text-end pe-4">Status Audit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($opname as $item)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span>
                            </td>
                            <td><span class="fw-bold text-dark">{{ $item->barang->nama_barang }}</span></td>
                            <td class="text-end"><span class="fw-bold text-dark">{{ number_format($item->stok_fisik) }}</span></td>
                            <td class="text-end">
                                <span class="fw-bold {{ $item->selisih > 0 ? 'text-success' : ($item->selisih < 0 ? 'text-danger' : 'text-secondary') }}">
                                    {{ $item->selisih > 0 ? '+' : '' }}{{ $item->selisih }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if ($item->selisih > 0)
                                    <span class="badge badge-subtle-success d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-plus-circle-fill"></i> Surplus (+{{ $item->selisih }})
                                    </span>
                                @elseif ($item->selisih < 0)
                                    <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-dash-circle-fill"></i> Defisit ({{ $item->selisih }})
                                    </span>
                                @else
                                    <span class="badge badge-subtle-secondary d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Sesuai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($opname->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $opname->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="p-5 text-center text-muted">
            <i class="bi bi-journal-text fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Belum Ada Riwayat Stock Opname</h5>
            <p class="small mb-3">Lakukan penyesuaian stok fisik barang terlebih dahulu.</p>
            @if (in_array(auth()->user()->role, ['admin', 'kepala_gudang']))
                <a href="{{ route('inventory.transaksi.opname.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-clipboard-check me-1"></i> Stock Opname Baru
                </a>
            @endif
        </div>
    @endif
</div>

<div class="alert alert-custom bg-light border p-3">
    <div class="d-flex align-items-start gap-2 text-secondary small">
        <i class="bi bi-info-circle-fill text-primary fs-6 mt-1"></i>
        <div>
            <strong>Keterangan Audit Opname:</strong>
            <ul class="mb-0 ps-3 mt-1">
                <li><strong>Surplus (+)</strong>: Barang di gudang fisik lebih banyak dibanding catatan database.</li>
                <li><strong>Defisit (-)</strong>: Barang di gudang fisik berkurang (rusak/hilang) dibanding catatan database.</li>
                <li><strong>Sesuai (=)</strong>: Jumlah barang fisik akurat 100% sesuai sistem.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
