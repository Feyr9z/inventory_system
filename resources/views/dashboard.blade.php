@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard Overview</h3>
        <p class="text-muted small mb-0">Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})</p>
    </div>
</div>

@if ($barang_minimum > 0)
    <div class="alert alert-warning alert-custom alert-dismissible fade show d-flex align-items-center justify-content-between mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0"></i>
            <div>
                <strong>Stock Alert!</strong> Terdapat <span class="badge bg-danger ms-1 me-1">{{ $barang_minimum }} item</span> dengan stok di bawah batas minimum.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('inventory.barang.index') }}" class="btn btn-sm btn-warning text-dark fw-semibold">
                Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Barang</span>
                    <h2 class="fw-bold text-dark mb-0">{{ number_format($total) }}</h2>
                </div>
                <div class="icon-box icon-box-primary">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Stok Unit</span>
                    <h2 class="fw-bold text-dark mb-0">{{ number_format($stok) }}</h2>
                </div>
                <div class="icon-box icon-box-success">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3" title="Jumlah barang dengan stok dibawah limit minimum">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Kurang Stok</span>
                    <h2 class="fw-bold {{ $barang_minimum > 0 ? 'text-danger' : 'text-dark' }} mb-0">{{ number_format($barang_minimum) }}</h2>
                </div>
                <div class="icon-box {{ $barang_minimum > 0 ? 'icon-box-danger' : 'icon-box-success' }}">
                    <i class="bi {{ $barang_minimum > 0 ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill' }}"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <span class="badge {{ $barang_minimum > 0 ? 'badge-subtle-danger' : 'badge-subtle-success' }}">
                    {{ $barang_minimum > 0 ? 'Perlu Restock' : 'Stok Aman' }}
                </span>
            </div>
        </div>
    </div>

    @if ($role === 'admin')
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Masuk (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($barang_masuk_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-info">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Keluar (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($barang_keluar_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-warning">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Opname (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($opname_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-purple">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($role === 'staff')
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Masuk (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($barang_masuk_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-info">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Keluar (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($barang_keluar_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-warning">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($role === 'management')
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Masuk (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($barang_masuk_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-info">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Keluar (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($barang_keluar_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-warning">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-elevated p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Opname (Bln Ini)</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($opname_bulan_ini ?? 0) }}</h2>
                    </div>
                    <div class="icon-box icon-box-purple">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Quick Access Section -->
<div class="card-elevated p-4">
    <div class="mb-3 pb-2 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">Akses Cepat</h5>
    </div>
    
    <div class="row g-3">
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <a href="{{ route('inventory.barang.index') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                <div class="icon-box icon-box-primary mx-auto mb-2">
                    <i class="bi bi-box-seam"></i>
                </div>
                <span class="fw-semibold small d-block">Daftar Barang</span>
            </a>
        </div>

        @if ($role === 'admin')
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.barang.create') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-success mx-auto mb-2">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <span class="fw-semibold small d-block">Tambah Barang</span>
                </a>
            </div>
        @endif

        @if ($role === 'admin' || $role === 'staff')
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.transaksi.masuk.create') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-info mx-auto mb-2">
                        <i class="bi bi-arrow-down-left-circle"></i>
                    </div>
                    <span class="fw-semibold small d-block">Barang Masuk</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.transaksi.keluar.create') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-warning mx-auto mb-2">
                        <i class="bi bi-arrow-up-right-circle"></i>
                    </div>
                    <span class="fw-semibold small d-block">Barang Keluar</span>
                </a>
            </div>
        @endif

        @if ($role === 'admin')
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.transaksi.opname.create') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-purple mx-auto mb-2">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <span class="fw-semibold small d-block">Stock Opname</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.transaksi.opname.history') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-info mx-auto mb-2">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <span class="fw-semibold small d-block">History Opname</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.laporan.stok') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-primary mx-auto mb-2">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                    <span class="fw-semibold small d-block">Laporan Stok</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.log-aktivitas') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-danger mx-auto mb-2">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <span class="fw-semibold small d-block">Log Aktivitas</span>
                </a>
            </div>
        @endif

        @if ($role === 'management')
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.laporan.stok') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-primary mx-auto mb-2">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                    <span class="fw-semibold small d-block">Laporan Stok</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.laporan.transaksi') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-info mx-auto mb-2">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <span class="fw-semibold small d-block">Laporan Transaksi</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.transaksi.opname.history') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-purple mx-auto mb-2">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <span class="fw-semibold small d-block">History Opname</span>
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('inventory.log-aktivitas') }}" class="card-elevated p-3 text-center text-decoration-none text-dark d-block h-100">
                    <div class="icon-box icon-box-danger mx-auto mb-2">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <span class="fw-semibold small d-block">Log Aktivitas</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
