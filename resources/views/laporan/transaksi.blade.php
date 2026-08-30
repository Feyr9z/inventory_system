@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Laporan Transaksi</h3>
        <p class="text-muted small mb-0">Analisis riwayat pergerakan stok barang masuk, barang keluar, dan penelusuran lot FIFO</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card-elevated p-4 mb-4">
    <h6 class="fw-bold text-dark mb-3">Filter Periode Transaksi</h6>
    <form action="{{ route('inventory.laporan.transaksi') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label for="dari_tanggal" class="form-label fw-semibold small text-secondary">Dari Tanggal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar text-muted"></i></span>
                <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control border-start-0 ps-0" value="{{ $dari_tanggal }}" required>
            </div>
        </div>

        <div class="col-md-3">
            <label for="sampai_tanggal" class="form-label fw-semibold small text-secondary">Sampai Tanggal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check text-muted"></i></span>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control border-start-0 ps-0" value="{{ $sampai_tanggal }}" required>
            </div>
        </div>

        <div class="col-md-3">
            <label for="tipe_transaksi" class="form-label fw-semibold small text-secondary">Tipe Transaksi</label>
            <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                <option value="semua" {{ $tipe_transaksi === 'semua' ? 'selected' : '' }}>Semua Transaksi</option>
                <option value="masuk" {{ $tipe_transaksi === 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="keluar" {{ $tipe_transaksi === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </div>

        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-search"></i> Tampilkan
            </button>
            @if ($data)
                <a href="{{ route('inventory.laporan.transaksi.export') }}?dari_tanggal={{ $dari_tanggal }}&sampai_tanggal={{ $sampai_tanggal }}&tipe_transaksi={{ $tipe_transaksi }}" class="btn btn-success fw-semibold d-flex align-items-center gap-1" title="Download CSV">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            @endif
        </div>
    </form>
</div>

@if ($data)
    @if ($total_masuk > 0 || $total_keluar > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card-elevated p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Barang Masuk</span>
                            <h2 class="fw-bold text-success mb-0">+{{ number_format($total_masuk) }}</h2>
                            <small class="text-muted">Unit masuk periode ini</small>
                        </div>
                        <div class="icon-box icon-box-success">
                            <i class="bi bi-arrow-down-left-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-elevated p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Barang Keluar</span>
                            <h2 class="fw-bold text-danger mb-0">-{{ number_format($total_keluar) }}</h2>
                            <small class="text-muted">Unit keluar periode ini</small>
                        </div>
                        <div class="icon-box icon-box-danger">
                            <i class="bi bi-arrow-up-right-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card-elevated overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Tipe</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Jumlah Unit</th>
                        <th>Keterangan / Sumber / Tujuan</th>
                        <th>Petugas</th>
                        <th class="pe-4">Alokasi & Lot FIFO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</span>
                            </td>
                            <td>
                                @if ($item['tipe'] === 'Masuk')
                                    <span class="badge badge-subtle-success d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-arrow-down-left-circle-fill"></i> Masuk
                                    </span>
                                @else
                                    <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-arrow-up-right-circle-fill"></i> Keluar
                                    </span>
                                @endif
                            </td>
                            <td><span class="fw-bold text-dark">{{ $item['nama_barang'] }}</span></td>
                            <td class="text-end">
                                <span class="fw-bold fs-6 {{ $item['tipe'] === 'Masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ $item['tipe'] === 'Masuk' ? '+' : '-' }}{{ number_format(abs($item['jumlah'])) }}
                                </span>
                            </td>
                            <td><span class="text-secondary small">{{ $item['keterangan'] }}</span></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person me-1"></i>{{ $item['petugas'] }}
                                </span>
                            </td>
                            <td class="pe-4">
                                @if ($item['tipe'] === 'Masuk')
                                    <span class="badge badge-subtle-info small" title="Sisa stok dari lot masuk ini">
                                        <i class="bi bi-box me-1"></i>Sisa Lot: {{ number_format($item['sisa_jumlah']) }} unit
                                    </span>
                                @elseif (!empty($item['fifo_info']))
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($item['fifo_info'] as $fifoItem)
                                            <span class="badge bg-light text-secondary border text-start font-monospace small" style="font-size: 0.75rem;">
                                                <i class="bi bi-arrow-return-right text-primary me-1"></i>{{ $fifoItem }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="card-elevated p-5 text-center text-muted mb-4">
        <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary"></i>
        <h5 class="fw-semibold text-dark">Tidak Ada Transaksi</h5>
        <p class="small mb-0">Tidak ditemukan data transaksi barang masuk maupun keluar pada periode tanggal tersebut.</p>
    </div>
@endif
@endsection
