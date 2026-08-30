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
    <h6 class="fw-bold text-dark mb-3">Filter & Pencarian Riwayat Transaksi</h6>
    <form action="{{ route('inventory.laporan.transaksi') }}" method="GET" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="dari_tanggal" class="form-label fw-semibold small text-secondary">Dari Tanggal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar text-muted"></i></span>
                <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control border-start-0 ps-0" value="{{ $dari_tanggal }}" required>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <label for="sampai_tanggal" class="form-label fw-semibold small text-secondary">Sampai Tanggal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check text-muted"></i></span>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control border-start-0 ps-0" value="{{ $sampai_tanggal }}" required>
            </div>
        </div>

        <div class="col-lg-2 col-md-4">
            <label for="tipe_transaksi" class="form-label fw-semibold small text-secondary">Tipe Transaksi</label>
            <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                <option value="semua" {{ $tipe_transaksi === 'semua' ? 'selected' : '' }}>Semua Transaksi</option>
                <option value="masuk" {{ $tipe_transaksi === 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="keluar" {{ $tipe_transaksi === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-4">
            <label for="sort" class="form-label fw-semibold small text-secondary">Urutan Tanggal</label>
            <select id="sort" name="sort" class="form-select">
                <option value="tanggal_desc" {{ ($sort ?? 'tanggal_desc') === 'tanggal_desc' ? 'selected' : '' }}>Terbaru di Atas</option>
                <option value="tanggal_asc" {{ ($sort ?? '') === 'tanggal_asc' ? 'selected' : '' }}>Terlama di Atas</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-4">
            <label for="search" class="form-label fw-semibold small text-secondary">Kata Kunci</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Barang/Tujuan/Petugas..." value="{{ $search ?? '' }}">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
            @if (request()->hasAny(['search', 'tipe_transaksi', 'sort']))
                <a href="{{ route('inventory.laporan.transaksi') }}" class="btn btn-app-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            @endif
            @if ($data)
                <a href="{{ route('inventory.laporan.transaksi.export') }}?dari_tanggal={{ $dari_tanggal }}&sampai_tanggal={{ $sampai_tanggal }}&tipe_transaksi={{ $tipe_transaksi }}&search={{ urlencode($search ?? '') }}&sort={{ $sort ?? 'tanggal_desc' }}" class="btn btn-success fw-semibold d-inline-flex align-items-center gap-1" title="Download CSV">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            @endif
            <button type="submit" class="btn btn-app-primary fw-semibold d-inline-flex align-items-center gap-1">
                <i class="bi bi-search"></i> Tampilkan Laporan
            </button>
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
                        <th class="ps-4">No. Dokumen & Tanggal</th>
                        <th>Tipe</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Jumlah Unit</th>
                        <th>Keterangan / Sumber / Tujuan</th>
                        <th>Petugas</th>
                        <th>Alokasi & Lot FIFO</th>
                        <th class="text-end pe-4">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $idx => $item)
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-dark d-block" style="font-size: 0.8rem;">{{ $item['doc_number'] }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</small>
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
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $item['nama_barang'] }}</span>
                                <small class="text-muted">{{ $item['kategori'] ?? '-' }}</small>
                            </td>
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
                            <td>
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
                            <td class="text-end pe-4">
                                <button type="button" class="btn-action-detail" data-bs-toggle="modal" data-bs-target="#receiptModal-{{ $idx }}" title="Lihat Bukti Transaksi">
                                    <i class="bi bi-receipt"></i> Bukti
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Struk / Bukti Transaksi -->
                        <div class="modal fade" id="receiptModal-{{ $idx }}" tabindex="-1" aria-labelledby="receiptLabel-{{ $idx }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <div class="modal-body p-4 p-md-5">
                                        <div class="receipt-card">
                                            <!-- Header Perusahaan -->
                                            <div class="receipt-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <div class="bg-dark text-white rounded p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                            <i class="bi bi-box-seam-fill fs-6"></i>
                                                        </div>
                                                        <span class="fw-bold text-dark fs-5 tracking-tight">PT ATHA ANAKHATULISTIWA</span>
                                                    </div>
                                                    <small class="text-muted text-uppercase fw-semibold d-block">
                                                        {{ $item['tipe'] === 'Masuk' ? 'Surat Bukti Penerimaan Barang' : 'Surat Jalan / Bukti Pengeluaran Barang' }}
                                                    </small>
                                                </div>
                                                <div class="text-sm-end">
                                                    <span class="receipt-doc-badge d-inline-block mb-1">{{ $item['doc_number'] }}</span>
                                                    <div class="small text-muted">Tanggal: <strong>{{ $item['tanggal_fmt'] }}</strong></div>
                                                </div>
                                            </div>

                                            <!-- Metadata Transaksi -->
                                            <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                                <div class="col-sm-6">
                                                    <div class="small text-muted fw-semibold text-uppercase">Petugas Input</div>
                                                    <div class="fw-bold text-dark">{{ $item['petugas'] }} <span class="fw-normal text-muted small">({{ $item['petugas_role'] ?? 'Staff' }})</span></div>
                                                    <small class="text-muted">Waktu: {{ $item['waktu_input'] ?? '-' }}</small>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="small text-muted fw-semibold text-uppercase">
                                                        {{ $item['tipe'] === 'Masuk' ? 'Pemasok / Sumber Barang' : 'Tujuan / Keperluan Pengeluaran' }}
                                                    </div>
                                                    <div class="fw-bold text-dark">{{ $item['keterangan'] }}</div>
                                                    <small class="text-muted">Lokasi Gudang: {{ $item['lokasi'] ?? 'Gudang Utama' }}</small>
                                                </div>
                                            </div>

                                            <!-- Tabel Barang -->
                                            <div class="table-responsive mb-4">
                                                <table class="table receipt-table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Barang</th>
                                                            <th>Kategori</th>
                                                            <th class="text-end">Jumlah Unit</th>
                                                            <th>Status Alokasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong class="text-dark">{{ $item['nama_barang'] }}</strong></td>
                                                            <td><span class="text-muted small">{{ $item['kategori'] ?? '-' }}</span></td>
                                                            <td class="text-end">
                                                                <span class="fw-bold fs-6 {{ $item['tipe'] === 'Masuk' ? 'text-success' : 'text-danger' }}">
                                                                    {{ number_format(abs($item['jumlah'])) }} Unit
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($item['tipe'] === 'Masuk')
                                                                    <span class="badge badge-subtle-success">Lot Baru Diinisialisasi</span>
                                                                @else
                                                                    <span class="badge badge-subtle-primary">Alokasi FIFO Terpenuhi</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Breakdown FIFO jika Keluar -->
                                            @if ($item['tipe'] === 'Keluar' && !empty($item['fifo_details']))
                                                <div class="mb-4 p-3 bg-light rounded-3 border">
                                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                                        <i class="bi bi-layers-fill text-primary me-1"></i> Rincian Konsumsi Lot FIFO:
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm mb-0 small">
                                                            <thead>
                                                                <tr class="text-muted">
                                                                    <th>Lot Ref</th>
                                                                    <th>Tgl Masuk Lot</th>
                                                                    <th>Pemasok Asal</th>
                                                                    <th class="text-end">Qty Diambil</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($item['fifo_details'] as $lot)
                                                                    <tr>
                                                                        <td class="font-monospace fw-bold">#{{ $lot['lot_id'] }}</td>
                                                                        <td>{{ $lot['lot_tanggal'] }}</td>
                                                                        <td class="text-muted">{{ $lot['lot_sumber'] }}</td>
                                                                        <td class="text-end fw-bold text-dark">{{ number_format($lot['jumlah_diambil']) }} Unit</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @elseif ($item['tipe'] === 'Masuk')
                                                <div class="mb-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="small text-muted text-uppercase fw-semibold d-block">ID Lot Masuk (FIFO Pool)</span>
                                                        <span class="font-monospace fw-bold text-dark fs-6">Lot #{{ $item['id'] }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="small text-muted text-uppercase fw-semibold d-block">Sisa Stok Lot Saat Ini</span>
                                                        <span class="fw-bold text-success fs-6">{{ number_format($item['sisa_jumlah']) }} Unit</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Kolom Verifikasi / Tanda Tangan -->
                                            <div class="row g-4 pt-3">
                                                <div class="col-6">
                                                    <div class="receipt-signature-box">
                                                        <div class="fw-bold text-dark">{{ $item['petugas'] }}</div>
                                                        <div class="text-muted small">Petugas Gudang</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="receipt-signature-box">
                                                        <div class="fw-bold text-dark">{{ $item['tipe'] === 'Masuk' ? 'Pengirim / Ekspedisi' : 'Penerima Barang' }}</div>
                                                        <div class="text-muted small">Tanda Tangan & Nama Terang</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top d-print-none">
                                            <button type="button" class="btn btn-app-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-lg"></i> Tutup
                                            </button>
                                            <button type="button" class="btn btn-app-primary" onclick="window.print()">
                                                <i class="bi bi-printer"></i> Cetak Dokumen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
