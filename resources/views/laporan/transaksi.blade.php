@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<h2 class="page-title mb-4">Laporan Transaksi</h2>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Filter Laporan</h5>
        <form action="{{ route('inventory.laporan.transaksi') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="dari_tanggal" class="form-label">Dari Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control" value="{{ $dari_tanggal }}" required>
            </div>

            <div class="col-md-3">
                <label for="sampai_tanggal" class="form-label">Sampai Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control" value="{{ $sampai_tanggal }}" required>
            </div>

            <div class="col-md-3">
                <label for="tipe_transaksi" class="form-label">Tipe Transaksi</label>
                <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                    <option value="semua" {{ $tipe_transaksi === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="masuk" {{ $tipe_transaksi === 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="keluar" {{ $tipe_transaksi === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">🔍 Tampilkan Laporan</button>
            </div>
        </form>
    </div>
</div>

@if ($data)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Nama Barang</th>
                    <th class="text-end">Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                        <td>
                            <span class="badge {{ $item['tipe'] === 'Masuk' ? 'bg-success' : 'bg-danger' }}">
                                {{ $item['tipe'] }}
                            </span>
                        </td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td class="text-end">{{ abs($item['jumlah']) }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info" role="alert">
        Tidak ada data transaksi untuk periode ini.
    </div>
@endif
@endsection
