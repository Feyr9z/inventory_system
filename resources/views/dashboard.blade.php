@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2 class="page-title">Dashboard</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>📦 Total Barang</h3>
        <div class="value">{{ $total }}</div>
    </div>
    <div class="stat-card success">
        <h3>📊 Total Stok</h3>
        <div class="value">{{ $stok }}</div>
    </div>
    <div class="stat-card {{ $barang_minimum > 0 ? 'danger' : 'success' }}">
        <h3>⚠️ Barang Minimum</h3>
        <div class="value">{{ $barang_minimum }}</div>
    </div>

    @if ($role === 'admin')
        <div class="stat-card info">
            <h3>📥 Masuk (Bln Ini)</h3>
            <div class="value">{{ $barang_masuk_bulan_ini ?? 0 }}</div>
        </div>
        <div class="stat-card warning">
            <h3>📤 Keluar (Bln Ini)</h3>
            <div class="value">{{ $barang_keluar_bulan_ini ?? 0 }}</div>
        </div>
        <div class="stat-card info">
            <h3>🔍 Opname (Bln Ini)</h3>
            <div class="value">{{ $opname_bulan_ini ?? 0 }}</div>
        </div>
    @elseif ($role === 'staff')
        <div class="stat-card info">
            <h3>📋 Transaksi (Bln Ini)</h3>
            <div class="value">{{ $transaksi_bulan_ini ?? 0 }}</div>
        </div>
    @endif
</div>

<div class="card">
    <h3>🚀 Akses Cepat</h3>
    <div class="quick-actions">
        <a href="{{ route('inventory.barang.index') }}" class="quick-action-btn">
            <div style="font-size: 1.5rem;">📋</div>
            Daftar Barang
        </a>

        @if ($role === 'admin')
            <a href="{{ route('inventory.barang.create') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">➕</div>
                Tambah Barang
            </a>
        @endif

        @if ($role === 'admin' || $role === 'staff')
            <a href="{{ route('inventory.transaksi.masuk.create') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">📥</div>
                Barang Masuk
            </a>
            <a href="{{ route('inventory.transaksi.keluar.create') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">📤</div>
                Barang Keluar
            </a>
        @endif

        @if ($role === 'admin')
            <a href="{{ route('inventory.transaksi.opname.create') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">🔍</div>
                Stock Opname
            </a>
            <a href="{{ route('inventory.laporan.stok') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">📊</div>
                Laporan Stok
            </a>
            <a href="{{ route('inventory.log-aktivitas') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">📝</div>
                Log Aktivitas
            </a>
        @endif

        @if ($role === 'management')
            <a href="{{ route('inventory.laporan.stok') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">📊</div>
                Laporan Stok
            </a>
            <a href="{{ route('inventory.laporan.transaksi') }}" class="quick-action-btn">
                <div style="font-size: 1.5rem;">📈</div>
                Laporan Transaksi
            </a>
        @endif
    </div>
</div>
@endsection
