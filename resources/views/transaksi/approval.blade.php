@extends('layouts.app')

@section('title', 'Pemeriksaan & Persetujuan Barang Keluar')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">Pemeriksaan Barang Keluar</h3>
        <p class="text-muted small mb-0">Otorisasi permohonan pengeluaran barang, eksekusi alokasi FIFO, dan verifikasi logistik</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-2 fs-6">
            <i class="bi bi-clock-history text-warning me-1"></i> Antrean Pending: <strong class="text-primary">{{ $countPending }}</strong>
        </span>
    </div>
</div>

<!-- Tabs Navigasi -->
<div class="d-flex gap-2 mb-4 border-bottom pb-2">
    <a href="{{ route('inventory.transaksi.approval.index', ['tab' => 'pending']) }}" class="btn btn-sm {{ $tab === 'pending' ? 'btn-dark fw-bold' : 'btn-light text-muted' }} px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
        <i class="bi bi-hourglass-split {{ $tab === 'pending' ? 'text-warning' : '' }}"></i>
        <span>Menunggu Persetujuan</span>
        @if ($countPending > 0)
            <span class="badge bg-danger rounded-pill">{{ $countPending }}</span>
        @endif
    </a>
    <a href="{{ route('inventory.transaksi.approval.index', ['tab' => 'riwayat']) }}" class="btn btn-sm {{ $tab === 'riwayat' ? 'btn-dark fw-bold' : 'btn-light text-muted' }} px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
        <i class="bi bi-check2-all {{ $tab === 'riwayat' ? 'text-success' : '' }}"></i>
        <span>Riwayat Pemeriksaan</span>
    </a>
</div>

<!-- Filter Card -->
<div class="card-elevated p-3 mb-4">
    <form action="{{ route('inventory.transaksi.approval.index') }}" method="GET" class="row g-2 align-items-center">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="col-lg-3 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari barang, tujuan, staff..." value="{{ $search }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-6">
            <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}" placeholder="Dari Tanggal" title="Dari Tanggal">
        </div>

        <div class="col-lg-2 col-md-4">
            <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}" placeholder="Sampai Tanggal" title="Sampai Tanggal">
        </div>

        @if ($tab === 'riwayat')
            <div class="col-lg-2 col-md-4">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
        @endif

        <div class="col-lg-2 col-md-4">
            <select name="sort" class="form-select">
                <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru di Atas</option>
                <option value="terlama" {{ $sort === 'terlama' ? 'selected' : '' }}>Terlama di Atas</option>
                <option value="jumlah_desc" {{ $sort === 'jumlah_desc' ? 'selected' : '' }}>Kuantitas Terbesar</option>
                <option value="jumlah_asc" {{ $sort === 'jumlah_asc' ? 'selected' : '' }}>Kuantitas Terkecil</option>
            </select>
        </div>

        <div class="col-lg-1 col-md-4 d-flex gap-1">
            <button type="submit" class="btn btn-app-primary w-100" title="Terapkan Filter">
                <i class="bi bi-filter"></i>
            </button>
            @if (request()->hasAny(['search', 'dari_tanggal', 'sampai_tanggal', 'status', 'sort']))
                <a href="{{ route('inventory.transaksi.approval.index', ['tab' => $tab]) }}" class="btn btn-app-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card-elevated overflow-hidden mb-4">
    @if ($pengajuan->count() > 0)
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Ref & Tanggal</th>
                        <th>Barang & Kategori</th>
                        <th class="text-end">Qty Diminta</th>
                        @if ($tab === 'pending')
                            <th class="text-end">Stok Gudang</th>
                        @endif
                        <th>Tujuan / Keperluan</th>
                        <th>Pemohon (Staff)</th>
                        @if ($tab === 'riwayat')
                            <th>Status & Pemeriksa</th>
                            <th>Keterangan / Alokasi FIFO</th>
                        @endif
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $idx => $item)
                        @php
                            $docNumber = 'OUT-' . $item->tanggal->format('Ymd') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);
                            $stokCukup = $item->barang->stok >= $item->jumlah;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-dark d-block" style="font-size: 0.825rem;">{{ $docNumber }}</span>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $item->tanggal->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('inventory.barang.show', $item->barang_id) }}" class="fw-bold text-dark text-decoration-none d-inline-flex align-items-center gap-1.5" title="Buka Master Detail Barang">
                                    <span class="hover-underline">{{ $item->barang->nama_barang }}</span>
                                    <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.725rem;"></i>
                                </a>
                                <span class="badge badge-subtle-secondary small mt-0.5 d-table"><i class="bi bi-tag me-1"></i>{{ $item->barang->kategori?->nama_kategori ?? '-' }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold fs-6 text-danger">-{{ number_format($item->jumlah) }}</span> <small class="text-muted">Unit</small>
                            </td>
                            @if ($tab === 'pending')
                                <td class="text-end">
                                    <span class="fw-bold fs-6 {{ $stokCukup ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($item->barang->stok) }}
                                    </span> <small class="text-muted">Unit</small>
                                    @if (!$stokCukup)
                                        <div class="badge bg-danger text-white small mt-0.5 d-block" style="font-size: 0.7rem;">Stok Kurang!</div>
                                    @endif
                                </td>
                            @endif
                            <td>
                                <span class="fw-semibold text-dark d-block small">{{ $item->tujuan }}</span>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $item->barang->lokasi ?? 'Gudang Utama' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person me-1"></i>{{ $item->user?->name ?? '-' }}
                                </span>
                            </td>
                            @if ($tab === 'riwayat')
                                <td>
                                    @if ($item->status === 'disetujui')
                                        <span class="badge badge-subtle-success d-inline-flex align-items-center gap-1 mb-1">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui
                                        </span>
                                    @else
                                        <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1 mb-1">
                                            <i class="bi bi-x-circle-fill"></i> Ditolak
                                        </span>
                                    @endif
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        Oleh: <strong>{{ $item->approver?->name ?? 'Kepala Gudang' }}</strong>
                                        <span class="d-block">{{ $item->approved_at ? $item->approved_at->format('d/m/Y H:i') : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->status === 'disetujui')
                                        <span class="badge bg-light text-secondary border font-monospace small" style="font-size: 0.75rem;">
                                            <i class="bi bi-layers-fill text-primary me-1"></i>{{ $item->details->count() }} Lot FIFO Terkonsumsi
                                        </span>
                                    @else
                                        <div class="p-1.5 bg-light rounded text-danger small" style="font-size: 0.75rem;">
                                            <strong>Alasan:</strong> {{ $item->catatan_penolakan ?? 'Tidak ada alasan penolakan tercatat' }}
                                        </div>
                                    @endif
                                </td>
                            @endif
                            <td class="text-end pe-4">
                                @if ($tab === 'pending')
                                    <div class="d-inline-flex gap-1.5">
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTolak-{{ $item->id }}" title="Tolak Pengajuan">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSetujui-{{ $item->id }}" title="Setujui & Eksekusi FIFO">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </div>
                                @else
                                    @if ($item->status === 'disetujui')
                                        <a href="{{ route('inventory.receipt.keluar', $item->id) }}?autoprint=1" target="_blank" class="btn-action-detail" title="Cetak Surat Jalan">
                                            <i class="bi bi-printer"></i> Surat Jalan
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($pengajuan->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $pengajuan->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="p-5 text-center text-muted">
            <i class="bi {{ $tab === 'pending' ? 'bi-clipboard-check text-success' : 'bi-inbox text-secondary' }} fs-1 d-block mb-2"></i>
            <h5 class="fw-semibold text-dark">
                {{ $tab === 'pending' ? 'Tidak Ada Pengajuan Menunggu Pemeriksaan' : 'Belum Ada Riwayat Pemeriksaan' }}
            </h5>
            <p class="small mb-0">
                {{ $tab === 'pending' ? 'Semua permohonan pengeluaran barang telah diproses dengan tuntas.' : 'Riwayat persetujuan atau penolakan pengeluaran barang akan ditampilkan di sini.' }}
            </p>
        </div>
    @endif
</div>

<!-- Modals Aksi Pemeriksaan di Luar Table Wrapper -->
@if ($pengajuan->count() > 0 && $tab === 'pending')
    @foreach ($pengajuan as $item)
        <!-- Modal Setujui -->
        <div class="modal fade" id="modalSetujui-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-success text-white rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-check2-circle fs-5"></i>
                            </div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Konfirmasi Persetujuan Pengeluaran</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">
                            Apakah Anda yakin ingin menyetujui pengeluaran barang ini? Sistem akan secara otomatis mengalokasikan <strong>Lot Persediaan FIFO Tertua</strong> dan mengurangi stok gudang.
                        </p>
                        <div class="p-3 bg-light rounded-3 border mb-3 small">
                            <div class="row g-2">
                                <div class="col-6 text-muted">Nama Barang:</div>
                                <div class="col-6 fw-bold text-dark text-end">{{ $item->barang->nama_barang }}</div>
                                <div class="col-6 text-muted">Kuantitas Dikeluarkan:</div>
                                <div class="col-6 fw-bold text-danger text-end">{{ number_format($item->jumlah) }} Unit</div>
                                <div class="col-6 text-muted">Tujuan / Keperluan:</div>
                                <div class="col-6 fw-semibold text-dark text-end">{{ $item->tujuan }}</div>
                                <div class="col-6 text-muted">Pemohon (Staff):</div>
                                <div class="col-6 fw-semibold text-dark text-end">{{ $item->user?->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-app-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('inventory.transaksi.approval.approve', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success fw-semibold px-4">
                                <i class="bi bi-check-lg me-1"></i> Ya, Setujui Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tolak -->
        <div class="modal fade" id="modalTolak-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <form action="{{ route('inventory.transaksi.approval.reject', $item->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-light border-bottom p-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-danger text-white rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-x-circle fs-5"></i>
                                </div>
                                <h5 class="modal-title fw-bold text-dark mb-0">Tolak Permohonan Pengeluaran</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-secondary small mb-3">
                                Permohonan pengeluaran barang akan dibatalkan. Stok barang dan lot FIFO <strong>tidak akan terpotong</strong>.
                            </p>
                            <div class="mb-3">
                                <label for="catatan_penolakan_{{ $item->id }}" class="form-label fw-bold small text-dark">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea name="catatan_penolakan" id="catatan_penolakan_{{ $item->id }}" rows="3" class="form-control" placeholder="Contoh: Dokumen SPK proyek belum lengkap / Salah input jumlah..." required minlength="3"></textarea>
                                <div class="form-text small">Alasan penolakan akan dapat dilihat oleh staf yang mengajukan.</div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-app-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger fw-semibold px-4">
                                <i class="bi bi-x-lg me-1"></i> Tolak Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
