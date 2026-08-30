<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan & Bukti Pengeluaran Barang - {{ $docNumber }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', system-ui, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 2rem 1rem;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .receipt-paper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 2.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
            }
            .receipt-paper {
                border: 1px solid #000000 !important;
                box-shadow: none !important;
                padding: 1.5rem !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>

<div class="print-container">
    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 border shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark font-monospace fs-6 px-3 py-2">{{ $docNumber }}</span>
            <span class="text-muted small">Pratinjau Dokumen Siap Cetak</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="window.close()" class="btn btn-sm btn-outline-secondary px-3 fw-semibold">
                <i class="bi bi-x-lg me-1"></i> Tutup
            </button>
            <button type="button" onclick="window.print()" class="btn btn-sm btn-primary px-4 fw-semibold">
                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen Sekarang
            </button>
        </div>
    </div>

    <!-- Printable Receipt Paper -->
    <div class="receipt-paper">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start pb-4 border-bottom border-2 border-dark mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="bg-dark text-white rounded p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                    <span class="fw-bold fs-4 text-dark tracking-tight">PT ATHA ANAKHATULISTIWA</span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase letter-spacing-1">
                    Surat Jalan & Bukti Pengeluaran Barang (Outbound Goods Issue)
                </div>
            </div>
            <div class="text-end">
                <div class="font-monospace fw-bold fs-5 text-dark">{{ $docNumber }}</div>
                <div class="text-muted small">Tanggal: <strong>{{ $keluar->tanggal->format('d/m/Y') }}</strong></div>
                <div class="text-muted small">Waktu Input: {{ $keluar->created_at ? $keluar->created_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>

        <!-- Metadata Transaksi -->
        <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
            <div class="col-6">
                <div class="text-muted small fw-semibold text-uppercase">Petugas Pengeluar Gudang</div>
                <div class="fw-bold fs-6 text-dark">{{ $keluar->user?->name ?? 'Staff Gudang' }}</div>
                <small class="text-muted">Peran: {{ $keluar->user ? (\App\Enums\Role::tryFrom($keluar->user->role)?->label() ?? ucfirst($keluar->user->role)) : '-' }}</small>
            </div>
            <div class="col-6">
                <div class="text-muted small fw-semibold text-uppercase">Tujuan / Proyek / Penerima</div>
                <div class="fw-bold fs-6 text-dark">{{ $keluar->tujuan }}</div>
                <small class="text-muted">Lokasi Asal: {{ $keluar->barang?->lokasi ?? 'Gudang Utama' }}</small>
            </div>
        </div>

        <!-- Tabel Barang Dikeluarkan -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="py-2">Nama Barang</th>
                        <th class="py-2">Kategori</th>
                        <th class="text-end py-2">Jumlah Unit Dikeluarkan</th>
                        <th class="py-2">Metode Alokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-3">
                            <strong class="text-dark fs-6">{{ $keluar->barang?->nama_barang ?? 'Barang #' . $keluar->barang_id }}</strong>
                        </td>
                        <td class="py-3 text-muted">{{ $keluar->barang?->kategori?->nama_kategori ?? '-' }}</td>
                        <td class="py-3 text-end">
                            <span class="fw-bold fs-5 text-danger">-{{ number_format($keluar->jumlah) }}</span> <span class="small text-muted">Unit</span>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-primary text-white">Alokasi FIFO Terpenuhi</span>
                            <div class="small text-muted mt-1">{{ $keluar->details->count() }} Lot Terkonsumsi</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Rincian Alokasi Lot FIFO Terkonsumsi -->
        <div class="p-3 bg-light rounded-3 border mb-5">
            <h6 class="fw-bold text-dark mb-3 small text-uppercase">
                <i class="bi bi-layers-fill text-primary me-1"></i> Rincian Konsumsi Lot FIFO (First In First Out):
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-0 small">
                    <thead class="table-secondary">
                        <tr>
                            <th class="py-1">Lot Ref</th>
                            <th class="py-1">Tgl Masuk Lot</th>
                            <th class="py-1">Pemasok / Sumber Asal</th>
                            <th class="text-end py-1">Qty Unit Diambil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keluar->details as $detail)
                            <tr>
                                <td class="font-monospace fw-bold py-1.5">Lot #{{ $detail->barang_masuk_id }}</td>
                                <td class="py-1.5">{{ $detail->barangMasuk?->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-muted py-1.5">{{ $detail->barangMasuk?->sumber ?? '-' }}</td>
                                <td class="text-end fw-bold text-dark py-1.5">{{ number_format($detail->jumlah_diambil) }} Unit</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-2">Tidak ada rincian lot</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Tanda Tangan -->
        <div class="row g-4 pt-4">
            <div class="col-6">
                <div class="text-center pt-5 border-top border-dark">
                    <div class="fw-bold text-dark fs-6">{{ $keluar->user?->name ?? 'Petugas Gudang' }}</div>
                    <div class="text-muted small">Petugas Gudang (Pengeluar)</div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center pt-5 border-top border-dark">
                    <div class="fw-bold text-dark fs-6">{{ $keluar->tujuan }}</div>
                    <div class="text-muted small">Penerima Barang / Driver Ekspedisi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (window.location.search.includes('autoprint=1')) {
        window.print();
    }
</script>

</body>
</html>
