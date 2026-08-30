@extends('layouts.app')

@section('title', 'Detail Barang & Lot FIFO - ' . $barang->nama_barang)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('inventory.barang.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Kembali ke Daftar Barang">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold text-dark mb-0">{{ $barang->nama_barang }}</h3>
            <span class="badge badge-subtle-info mt-1">
                <i class="bi bi-tag-fill me-1"></i>{{ $barang->kategori?->nama_kategori ?? 'Tanpa Kategori' }}
            </span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if (in_array(auth()->user()->role, ['admin', 'kepala_gudang']))
            <a href="{{ route('inventory.barang.edit', $barang->id) }}" class="btn-app-secondary">
                <i class="bi bi-pencil"></i> Edit Barang
            </a>
        @endif
        @if (in_array(auth()->user()->role, ['admin', 'staff']))
            <a href="{{ route('inventory.transaksi.masuk.create') }}" class="btn-app-secondary">
                <i class="bi bi-arrow-down-left-circle"></i> Input Masuk
            </a>
            <a href="{{ route('inventory.transaksi.keluar.create') }}" class="btn-app-primary">
                <i class="bi bi-arrow-up-right-circle"></i> Input Keluar
            </a>
        @endif
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Stok Sistem</span>
                    <h2 class="fw-bold fs-3 text-dark mb-0">{{ number_format($barang->stok) }} <span class="fs-6 text-muted fw-normal">unit</span></h2>
                </div>
                <div class="icon-box icon-box-primary">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted">Stok agregat di database</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Stok Minimum</span>
                    <h2 class="fw-bold fs-3 text-dark mb-0">{{ number_format($barang->stok_minimum) }} <span class="fs-6 text-muted fw-normal">unit</span></h2>
                </div>
                <div class="icon-box icon-box-warning">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                @if ($barang->stok < $barang->stok_minimum)
                    <span class="badge badge-subtle-danger">
                        <i class="bi bi-exclamation-circle-fill me-1"></i>Di Bawah Batas Minimum
                    </span>
                @else
                    <span class="badge badge-subtle-success">
                        <i class="bi bi-check-circle-fill me-1"></i>Stok Masih Aman
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Pool Lot Aktif (FIFO)</span>
                    @php $activeLotsCount = $barang->barangMasuk->where('sisa_jumlah', '>', 0)->count(); @endphp
                    <h2 class="fw-bold fs-3 text-dark mb-0">{{ number_format($activeLotsCount) }} <span class="fs-6 text-muted fw-normal">lot</span></h2>
                </div>
                <div class="icon-box icon-box-info">
                    <i class="bi bi-layers-fill"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                @php $totalSisaLot = $barang->barangMasuk->sum('sisa_jumlah'); @endphp
                <small class="text-muted">Total unit lot aktif: <strong>{{ number_format($totalSisaLot) }} unit</strong></small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Lokasi Penyimpanan</span>
                    <h5 class="fw-bold text-dark mb-0">{{ $barang->lokasi ?: 'Gudang Utama' }}</h5>
                </div>
                <div class="icon-box icon-box-purple">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted">Area penempatan barang fisik</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Pool Lot Masuk FIFO -->
    <div class="col-lg-7">
        <div class="card-elevated p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-box-arrow-in-down text-success me-2"></i>Pool Lot Masuk (FIFO Lot Tracking)
                    </h5>
                    <p class="text-muted small mb-0">Urutan prioritas lot yang akan dikonsumsi terlebih dahulu saat barang keluar</p>
                </div>
            </div>

            @if ($barang->barangMasuk->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">ID Lot</th>
                                <th>Tanggal</th>
                                <th>Sumber / Pemasok</th>
                                <th class="text-end">Awal</th>
                                <th class="text-end">Sisa (FIFO)</th>
                                <th>Status Lot</th>
                                <th class="pe-3">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barang->barangMasuk as $masuk)
                                <tr class="{{ $masuk->sisa_jumlah > 0 ? '' : 'opacity-75 bg-light' }}">
                                    <td class="ps-3">
                                        <span class="font-monospace fw-bold text-secondary">#{{ $masuk->id }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $masuk->tanggal ? $masuk->tanggal->format('d/m/Y') : '-' }}</span>
                                    </td>
                                    <td><span class="small text-secondary">{{ $masuk->sumber }}</span></td>
                                    <td class="text-end"><span class="text-secondary">{{ number_format($masuk->jumlah) }}</span></td>
                                    <td class="text-end">
                                        <span class="fw-bold {{ $masuk->sisa_jumlah > 0 ? 'text-success' : 'text-muted' }}">
                                            {{ number_format($masuk->sisa_jumlah) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($masuk->sisa_jumlah == $masuk->jumlah)
                                            <span class="badge badge-subtle-success">Utuh</span>
                                        @elseif ($masuk->sisa_jumlah > 0)
                                            <span class="badge badge-subtle-warning">Sebagian</span>
                                        @else
                                            <span class="badge badge-subtle-secondary">Habis</span>
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        <small class="text-muted">{{ $masuk->user?->name ?? '-' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                    <p class="small mb-0">Belum ada riwayat lot barang masuk untuk barang ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Kolom Kanan: Riwayat Pengeluaran & Detail Konsumsi FIFO -->
    <div class="col-lg-5">
        <div class="card-elevated p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-box-arrow-up-right text-warning me-2"></i>Riwayat Pengeluaran (FIFO Out)
                    </h5>
                    <p class="text-muted small mb-0">Catatan pemakaian lot masuk untuk setiap transaksi keluar</p>
                </div>
            </div>

            @if ($barang->barangKeluar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>Tujuan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="pe-3">Detail Alokasi Lot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barang->barangKeluar as $keluar)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-semibold text-dark">{{ $keluar->tanggal ? $keluar->tanggal->format('d/m/Y') : '-' }}</span>
                                        <div class="text-muted small" style="font-size: 0.75rem;">Oleh: {{ $keluar->user?->name ?? '-' }}</div>
                                    </td>
                                    <td><span class="small text-secondary">{{ $keluar->tujuan }}</span></td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger">-{{ number_format($keluar->jumlah) }}</span>
                                    </td>
                                    <td class="pe-3">
                                        @if ($keluar->details->count() > 0)
                                            <div class="d-flex flex-column gap-1">
                                                @foreach ($keluar->details as $d)
                                                    <span class="badge bg-light text-secondary border text-start font-monospace small" style="font-size: 0.75rem;">
                                                        <i class="bi bi-box-arrow-right text-primary me-1"></i>Lot #{{ $d->barang_masuk_id }}: {{ $d->jumlah_diambil }} unit
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
            @else
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-clipboard-x fs-2 d-block mb-2 text-secondary"></i>
                    <p class="small mb-0">Belum ada riwayat transaksi barang keluar untuk barang ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
