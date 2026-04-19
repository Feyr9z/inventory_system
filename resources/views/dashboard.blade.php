@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Dashboard</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Barang</h3>
        <div class="value">{{ $total }}</div>
    </div>
    <div class="stat-card">
        <h3>Total Stok</h3>
        <div class="value">{{ $stok }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1rem;">Navigasi Cepat</h3>
    <a href="{{ route('inventory.barang.index') }}" class="btn">Lihat Daftar Barang</a>
    <a href="{{ route('inventory.barang.create') }}" class="btn btn-success">Tambah Barang Baru</a>
    <a href="{{ route('inventory.transaksi.masuk.create') }}" class="btn">Masuk Barang</a>
    <a href="{{ route('inventory.transaksi.keluar.create') }}" class="btn">Keluar Barang</a>
    <a href="{{ route('inventory.transaksi.opname.create') }}" class="btn">Stock Opname</a>
</div>
@endsection
