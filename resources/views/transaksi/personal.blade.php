@extends('layouts.app')

@section('title', 'Riwayat Transaksi Saya')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Riwayat Transaksi Saya</h3>
        <p class="text-muted small mb-0">Catatan transaksi operasional barang masuk dan keluar yang diinput oleh <strong>{{ $user->name }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('inventory.transaksi.masuk.create') }}" class="btn-app-secondary">
            <i class="bi bi-arrow-down-left-circle"></i> Input Masuk
        </a>
        <a href="{{ route('inventory.transaksi.keluar.create') }}" class="btn-app-primary">
            <i class="bi bi-arrow-up-right-circle"></i> Input Keluar
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Aktivitas Saya</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($total_transaksi) }}</h3>
                    <small class="text-muted">Transaksi tercatat</small>
                </div>
                <div class="icon-box icon-box-primary">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Masuk Saya</span>
                    <h3 class="fw-bold text-success mb-0">+{{ number_format($total_masuk) }}</h3>
                    <small class="text-muted">Unit diterima</small>
                </div>
                <div class="icon-box icon-box-success">
                    <i class="bi bi-arrow-down-left-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-elevated p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Keluar Saya</span>
                    <h3 class="fw-bold text-danger mb-0">-{{ number_format($total_keluar) }}</h3>
                    <small class="text-muted">Unit dikeluarkan</small>
                </div>
                <div class="icon-box icon-box-danger">
                    <i class="bi bi-arrow-up-right-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card-elevated p-4 mb-4">
    <h6 class="fw-bold text-dark mb-3">Filter & Pencarian Transaksi Personal</h6>
    <form action="{{ route('inventory.transaksi.saya') }}" method="GET" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="dari_tanggal" class="form-label fw-semibold small text-secondary">Dari Tanggal</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar text-muted"></i></span>
                <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control border-start-0 ps-0" value="{{ $dari_tanggal }}">
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <label for="sampai_tanggal" class="form-label fw-semibold small text-secondary">Sampai Tanggal</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check text-muted"></i></span>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control border-start-0 ps-0" value="{{ $sampai_tanggal }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-4">
            <label for="tipe_transaksi" class="form-label fw-semibold small text-secondary">Tipe Transaksi</label>
            <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                <option value="semua" {{ $tipe_transaksi === 'semua' ? 'selected' : '' }}>Semua Tipe</option>
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
            <input type="text" id="search" name="search" class="form-control" placeholder="Barang/Tujuan/Sumber..." value="{{ $search ?? '' }}">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
            @if (request()->hasAny(['search', 'tipe_transaksi', 'sort', 'dari_tanggal', 'sampai_tanggal']))
                <a href="{{ route('inventory.transaksi.saya') }}" class="btn btn-app-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            @endif
            <button type="submit" class="btn btn-app-primary fw-semibold d-inline-flex align-items-center gap-1">
                <i class="bi bi-filter"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
@if (count($data) > 0)
    <div class="card-elevated overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Ref & Tanggal</th>
                        <th>Tipe</th>
                        <th>Nama Barang & Kategori</th>
                        <th class="text-end">Kuantitas</th>
                        <th>Keterangan / Alur</th>
                        <th>Status FIFO / Sisa Lot</th>
                        <th class="text-end pe-4">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $idx => $item)
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-dark d-block" style="font-size: 0.825rem;">{{ $item['doc_number'] }}</span>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</small>
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
                                <a href="{{ route('inventory.barang.show', $item['barang_id']) }}" class="fw-bold text-dark text-decoration-none d-inline-flex align-items-center gap-1.5" title="Buka Detail Barang & Lot Pool">
                                    <span class="hover-underline">{{ $item['nama_barang'] }}</span>
                                    <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.725rem;"></i>
                                </a>
                                <span class="badge badge-subtle-secondary small mt-0.5 d-table"><i class="bi bi-tag me-1"></i>{{ $item['kategori'] ?? '-' }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold fs-6 {{ $item['tipe'] === 'Masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ $item['tipe'] === 'Masuk' ? '+' : '-' }}{{ number_format(abs($item['jumlah'])) }} <small class="text-muted fw-normal">Unit</small>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark d-block small">{{ $item['keterangan'] }}</span>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $item['lokasi'] ?? 'Gudang Utama' }}</small>
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
                                <button type="button" class="btn-action-detail" data-bs-toggle="modal" data-bs-target="#personalReceiptModal-{{ $idx }}" title="Lihat Bukti Transaksi">
                                    <i class="bi bi-receipt"></i> Bukti
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals Bagian Bawah Di Luar Elemen Table -->
    @foreach ($data as $idx => $item)
        <div class="modal fade" id="personalReceiptModal-{{ $idx }}" tabindex="-1" aria-labelledby="receiptLabel-{{ $idx }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-body p-4 p-md-5">
                        <div class="receipt-card">
                            <!-- Header Perusahaan (Kop Surat Resmi) -->
                            <div class="receipt-header pb-3 mb-4">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <div class="bg-dark text-white rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-box-seam-fill fs-5"></i>
                                            </div>
                                            <span class="fw-bold text-dark fs-4 tracking-tight">PT ATHA ANAKHATULISTIWA</span>
                                        </div>
                                        <div class="text-muted text-uppercase fw-semibold small" style="font-size: 0.75rem; letter-spacing: 0.05em;">Divisi Manajemen Logistik & Inventoris Gudang</div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">Jl. Raya Industri Grafika No. 88 • Telp: (021) 555-0199 • Email: logistic@atha.co.id</div>
                                    </div>
                                    <div class="text-sm-end mt-2 mt-sm-0">
                                        <span class="receipt-doc-badge d-inline-block mb-1">{{ $item['doc_number'] }}</span>
                                        <div class="small text-muted">Tanggal: <strong>{{ $item['tanggal_fmt'] }}</strong></div>
                                        <div class="small text-muted">Waktu: {{ $item['waktu_input'] ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Judul Dokumen Bar -->
                            <div class="p-2.5 px-3 bg-light rounded-3 border mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.05em;">
                                        {{ $item['tipe'] === 'Masuk' ? 'Surat Bukti Penerimaan Barang' : 'Surat Jalan & Bukti Pengeluaran Barang' }}
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        {{ $item['tipe'] === 'Masuk' ? 'Inbound Goods Receipt & Lot Initialization Slip' : 'Outbound Goods Issue & FIFO Allocation Slip' }}
                                    </small>
                                </div>
                                <span class="badge {{ $item['tipe'] === 'Masuk' ? 'badge-subtle-success' : 'badge-subtle-danger' }}">
                                    {{ $item['tipe'] === 'Masuk' ? 'Barang Masuk' : 'Barang Keluar' }}
                                </span>
                            </div>

                            <!-- Metadata Transaksi -->
                            <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                <div class="col-sm-6">
                                    <div class="small text-muted fw-semibold text-uppercase">Petugas Input Gudang</div>
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
                                            <td>
                                                <a href="{{ route('inventory.barang.show', $item['barang_id']) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-inline-flex align-items-center gap-1.5" title="Buka Detail Barang di Tab Baru">
                                                    <span>{{ $item['nama_barang'] }}</span>
                                                    <i class="bi bi-box-arrow-up-right text-primary" style="font-size: 0.75rem;"></i>
                                                </a>
                                            </td>
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
                            <div class="row g-4 pt-4">
                                <div class="col-6">
                                    <div class="receipt-signature-box">
                                        <div class="fw-bold text-dark">{{ $item['petugas'] }}</div>
                                        <div class="text-muted small">Petugas Gudang (Staff)</div>
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
                            <a href="{{ route($item['tipe'] === 'Masuk' ? 'inventory.receipt.masuk' : 'inventory.receipt.keluar', $item['id']) }}?autoprint=1" target="_blank" class="btn btn-app-primary">
                                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="card-elevated p-5 text-center text-muted mb-4">
        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
        <h5 class="fw-semibold text-dark">Belum Ada Transaksi Tercatat</h5>
        <p class="small mb-3">Anda belum mencatat transaksi barang masuk maupun keluar pada periode tanggal ini.</p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('inventory.transaksi.masuk.create') }}" class="btn-app-secondary">
                <i class="bi bi-arrow-down-left-circle"></i> Input Masuk
            </a>
            <a href="{{ route('inventory.transaksi.keluar.create') }}" class="btn-app-primary">
                <i class="bi bi-arrow-up-right-circle"></i> Input Keluar
            </a>
        </div>
    </div>
@endif
@endsection
