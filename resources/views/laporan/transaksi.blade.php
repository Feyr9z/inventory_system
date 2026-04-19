@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="mb-4">
    <h2 class="page-title">Laporan Transaksi</h2>
    <p class="text-muted">Lihat riwayat barang masuk dan barang keluar dalam periode waktu tertentu. Membantu tracking pergerakan stok dan analisis kebutuhan.</p>
</div>

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

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">🔍 Tampilkan Laporan</button>
                @if ($data)
                    <a href="{{ route('inventory.laporan.transaksi.export') }}?dari_tanggal={{ $dari_tanggal }}&sampai_tanggal={{ $sampai_tanggal }}&tipe_transaksi={{ $tipe_transaksi }}" class="btn btn-success" title="Download as CSV">
                        📥 CSV
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if ($data)
    @if ($total_masuk > 0 || $total_keluar > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stat-card success">
                    <h5>📥 Total Barang Masuk</h5>
                    <div class="stat-value">{{ number_format($total_masuk) }}</div>
                    <small class="text-muted">unit dalam periode ini</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card danger">
                    <h5>📤 Total Barang Keluar</h5>
                    <div class="stat-value">{{ number_format($total_keluar) }}</div>
                    <small class="text-muted">unit dalam periode ini</small>
                </div>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Nama Barang</th>
                    <th class="text-end">Jumlah</th>
                    <th>Sumber/Tujuan</th>
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
                        <td class="text-end"><strong>{{ abs($item['jumlah']) }}</strong></td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3 text-muted">
        <small>📌 <strong>Penjelasan:</strong> Barang Masuk = penambahan stok dari supplier/sumber | Barang Keluar = pengurangan stok ke tujuan | Total = ringkasan pergerakan barang dalam periode</small>
    </div>
@else
    <div class="alert alert-info" role="alert">
        Tidak ada data transaksi untuk periode ini.
    </div>
@endif
@endsection
