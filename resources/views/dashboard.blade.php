@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h2 class="page-title">Dashboard</h2>
    @if ($barang_minimum > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>⚠️ Stock Alert!</strong> Ada {{ $barang_minimum }} barang dengan stok dibawah minimum.
            <a href="{{ route('inventory.barang.index') }}" class="alert-link">Lihat detail</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stat-card">
            <h5>📦 Total Barang</h5>
            <div class="stat-value">{{ $total }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stat-card success">
            <h5>📊 Total Stok</h5>
            <div class="stat-value">{{ $stok }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stat-card {{ $barang_minimum > 0 ? 'danger' : 'success' }}" title="Jumlah barang dengan stok dibawah limit minimum yang ditetapkan">
            <h5>⚠️ Barang Kurang Stok</h5>
            <div class="stat-value">{{ $barang_minimum }}</div>
            <small class="text-muted d-block mt-2">item perlu restock</small>
        </div>
    </div>

    @if ($role === 'admin')
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card info">
                <h5>📥 Masuk (Bln Ini)</h5>
                <div class="stat-value">{{ $barang_masuk_bulan_ini ?? 0 }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card warning">
                <h5>📤 Keluar (Bln Ini)</h5>
                <div class="stat-value">{{ $barang_keluar_bulan_ini ?? 0 }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card info">
                <h5>🔍 Opname (Bln Ini)</h5>
                <div class="stat-value">{{ $opname_bulan_ini ?? 0 }}</div>
            </div>
        </div>
    @elseif ($role === 'staff')
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card info">
                <h5>📋 Transaksi (Bln Ini)</h5>
                <div class="stat-value">{{ $transaksi_bulan_ini ?? 0 }}</div>
            </div>
        </div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🚀 Akses Cepat</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <a href="{{ route('inventory.barang.index') }}" class="btn btn-outline-primary w-100">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📋</div>
                    <small>Daftar Barang</small>
                </a>
            </div>

            @if ($role === 'admin')
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.barang.create') }}" class="btn btn-outline-success w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">➕</div>
                        <small>Tambah Barang</small>
                    </a>
                </div>
            @endif

            @if ($role === 'admin' || $role === 'staff')
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.transaksi.masuk.create') }}" class="btn btn-outline-info w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📥</div>
                        <small>Barang Masuk</small>
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.transaksi.keluar.create') }}" class="btn btn-outline-warning w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📤</div>
                        <small>Barang Keluar</small>
                    </a>
                </div>
            @endif

            @if ($role === 'admin')
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.transaksi.opname.create') }}" class="btn btn-outline-secondary w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🔍</div>
                        <small>Stock Opname</small>
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.opname.history') }}" class="btn btn-outline-info w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📋</div>
                        <small>History Opname</small>
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.laporan.stok') }}" class="btn btn-outline-primary w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                        <small>Laporan Stok</small>
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.log-aktivitas') }}" class="btn btn-outline-dark w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📝</div>
                        <small>Log Aktivitas</small>
                    </a>
                </div>
            @endif

            @if ($role === 'management')
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.laporan.stok') }}" class="btn btn-outline-primary w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                        <small>Laporan Stok</small>
                    </a>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('inventory.laporan.transaksi') }}" class="btn btn-outline-primary w-100">
                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📈</div>
                        <small>Laporan Transaksi</small>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
