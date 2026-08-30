@extends('layouts.app')

@section('title', 'History Stock Opname')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">History Stock Opname</h3>
        <p class="text-muted small mb-0">Riwayat audit perhitungan fisik, rekonsiliasi selisih stok, dan berita acara inventaris</p>
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

<!-- Table Card -->
<div class="card-elevated overflow-hidden mb-4">
    @if ($opname->count() > 0)
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Dokumen & Tanggal</th>
                        <th>Nama Barang & Kategori</th>
                        <th class="text-end">Stok Fisik</th>
                        <th class="text-end">Stok Sebelum</th>
                        <th class="text-end">Selisih Unit</th>
                        <th>Status Audit</th>
                        <th class="text-end pe-4">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($opname as $idx => $item)
                        @php
                            $docNumber = 'OPN-' . \Carbon\Carbon::parse($item->tanggal)->format('Ymd') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);
                            $stokSistem = $item->stok_fisik - $item->selisih;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-dark d-block" style="font-size: 0.825rem;">{{ $docNumber }}</span>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $item->barang->nama_barang }}</span>
                                <span class="badge badge-subtle-secondary small mt-0.5"><i class="bi bi-tag me-1"></i>{{ $item->barang->kategori?->nama_kategori ?? '-' }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark fs-6">{{ number_format($item->stok_fisik) }}</span> <small class="text-muted">Unit</small>
                            </td>
                            <td class="text-end">
                                <span class="text-muted fw-semibold">{{ number_format($stokSistem) }}</span> <small class="text-muted">Unit</small>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold fs-6 {{ $item->selisih > 0 ? 'text-success' : ($item->selisih < 0 ? 'text-danger' : 'text-secondary') }}">
                                    {{ $item->selisih > 0 ? '+' : '' }}{{ number_format($item->selisih) }}
                                </span>
                            </td>
                            <td>
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
                                        <i class="bi bi-check-circle-fill"></i> Sesuai (0)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn-action-detail" data-bs-toggle="modal" data-bs-target="#opnameReceipt-{{ $idx }}" title="Lihat Berita Acara">
                                    <i class="bi bi-receipt"></i> Berita Acara
                                </button>
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
            <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary"></i>
            <h5 class="fw-semibold text-dark">Belum Ada Riwayat Stock Opname</h5>
            <p class="small mb-3">Lakukan stock opname fisik untuk menyelaraskan catatan sistem dengan stok aktual di gudang.</p>
            @if (in_array(auth()->user()->role, ['admin', 'kepala_gudang']))
                <a href="{{ route('inventory.transaksi.opname.create') }}" class="btn-app-primary">
                    <i class="bi bi-clipboard-check me-1"></i> Mulai Stock Opname Baru
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Modals Berita Acara Di Luar Elemen Table -->
@if ($opname->count() > 0)
    @foreach ($opname as $idx => $item)
        @php
            $docNumber = 'OPN-' . \Carbon\Carbon::parse($item->tanggal)->format('Ymd') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);
            $stokSistem = $item->stok_fisik - $item->selisih;
        @endphp
        <div class="modal fade" id="opnameReceipt-{{ $idx }}" tabindex="-1" aria-labelledby="opnameLabel-{{ $idx }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-body p-4 p-md-5">
                        <div class="receipt-card">
                            <!-- Header Berita Acara -->
                            <div class="receipt-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 pb-3 border-bottom border-2 border-dark mb-4">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="bg-dark text-white rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <i class="bi bi-clipboard2-check-fill fs-6"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-5 tracking-tight">PT ATHA ANAKHATULISTIWA</span>
                                    </div>
                                    <small class="text-muted text-uppercase fw-semibold d-block">
                                        Berita Acara Rekonsiliasi & Stock Opname Fisik
                                    </small>
                                </div>
                                <div class="text-sm-end">
                                    <span class="receipt-doc-badge d-inline-block mb-1">{{ $docNumber }}</span>
                                    <div class="small text-muted">Tanggal Audit: <strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</strong></div>
                                </div>
                            </div>

                            <!-- Metadata Audit -->
                            <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                <div class="col-sm-6">
                                    <div class="small text-muted fw-semibold text-uppercase">Barang Yang Diaudit</div>
                                    <div class="fw-bold text-dark fs-6">{{ $item->barang->nama_barang }}</div>
                                    <small class="text-muted">Kategori: {{ $item->barang->kategori?->nama_kategori ?? '-' }}</small>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted fw-semibold text-uppercase">Lokasi Fisik</div>
                                    <div class="fw-bold text-dark">{{ $item->barang->lokasi ?? 'Gudang Utama' }}</div>
                                    <small class="text-muted">Audit Record ID: #{{ $item->id }}</small>
                                </div>
                            </div>

                            <!-- Tabel Rekonsiliasi -->
                            <div class="table-responsive mb-4">
                                <table class="table receipt-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Parameter Audit</th>
                                            <th class="text-end">Jumlah Unit</th>
                                            <th>Keterangan Rekonsiliasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Stok Sebelum Opname (Sistem)</td>
                                            <td class="text-end fw-semibold text-secondary">{{ number_format($stokSistem) }} Unit</td>
                                            <td class="text-muted small">Catatan sistem sebelum audit</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hasil Perhitungan Fisik Riil</strong></td>
                                            <td class="text-end fw-bold text-dark fs-6">{{ number_format($item->stok_fisik) }} Unit</td>
                                            <td><span class="badge badge-subtle-primary">Hasil Audit Aktual</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Selisih (Discrepancy)</strong></td>
                                            <td class="text-end fw-bold fs-6 {{ $item->selisih > 0 ? 'text-success' : ($item->selisih < 0 ? 'text-danger' : 'text-secondary') }}">
                                                {{ $item->selisih > 0 ? '+' : '' }}{{ number_format($item->selisih) }} Unit
                                            </td>
                                            <td>
                                                @if ($item->selisih > 0)
                                                    <span class="text-success small fw-semibold"><i class="bi bi-arrow-up-circle me-1"></i>Surplus Stok Fisik</span>
                                                @elseif ($item->selisih < 0)
                                                    <span class="text-danger small fw-semibold"><i class="bi bi-arrow-down-circle me-1"></i>Defisit Stok Fisik</span>
                                                @else
                                                    <span class="text-secondary small fw-semibold"><i class="bi bi-check-circle me-1"></i>Stok 100% Sesuai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Status Lot Reconciliation Info -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-1 small text-uppercase">
                                    <i class="bi bi-info-circle text-primary me-1"></i> Rekonsiliasi Lot FIFO Terkait:
                                </h6>
                                <p class="mb-0 small text-secondary">
                                    @if ($item->selisih > 0)
                                        Sistem otomatis menginisialisasi <strong>Lot Masuk Penyesuaian Opname</strong> sebanyak <strong>{{ $item->selisih }} unit</strong> pada tanggal audit agar FIFO lot pool tetap sinkron.
                                    @elseif ($item->selisih < 0)
                                        Sistem otomatis memotong saldo <strong>{{ abs($item->selisih) }} unit</strong> dari <strong>Lot FIFO Aktif Tertua</strong> yang masih memiliki saldo untuk menjaga konsistensi alokasi.
                                    @else
                                        Tidak ada penyesuaian lot yang diperlukan karena stok fisik identik dengan stok pada sistem inventaris.
                                    @endif
                                </p>
                            </div>

                            <!-- Kolom Verifikasi / Tanda Tangan -->
                            <div class="row g-4 pt-4">
                                <div class="col-6">
                                    <div class="receipt-signature-box">
                                        <div class="fw-bold text-dark">Petugas Auditor Fisik</div>
                                        <div class="text-muted small">Pelaksana Perhitungan Fisik</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="receipt-signature-box">
                                        <div class="fw-bold text-dark">Kepala Gudang / Supervisor</div>
                                        <div class="text-muted small">Verifikasi & Otorisasi Audit</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top d-print-none">
                            <button type="button" class="btn btn-app-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i> Tutup
                            </button>
                            <a href="{{ route('inventory.receipt.opname', $item->id) }}?autoprint=1" target="_blank" class="btn btn-app-primary">
                                <i class="bi bi-printer-fill me-1"></i> Cetak Berita Acara
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
