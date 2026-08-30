<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Stock Opname Fisik - {{ $docNumber }}</title>
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
                        <i class="bi bi-clipboard2-check-fill fs-5"></i>
                    </div>
                    <span class="fw-bold fs-4 text-dark tracking-tight">PT ATHA ANAKHATULISTIWA</span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase letter-spacing-1">
                    Berita Acara Rekonsiliasi & Stock Opname Fisik
                </div>
            </div>
            <div class="text-end">
                <div class="font-monospace fw-bold fs-5 text-dark">{{ $docNumber }}</div>
                <div class="text-muted small">Tanggal Audit: <strong>{{ \Carbon\Carbon::parse($opname->tanggal)->format('d/m/Y') }}</strong></div>
                <div class="text-muted small">Waktu Pencatatan: {{ $opname->created_at ? $opname->created_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>

        <!-- Metadata Audit -->
        <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
            <div class="col-6">
                <div class="text-muted small fw-semibold text-uppercase">Barang Yang Diaudit</div>
                <div class="fw-bold fs-6 text-dark">{{ $opname->barang?->nama_barang ?? 'Barang #' . $opname->barang_id }}</div>
                <small class="text-muted">Kategori: {{ $opname->barang?->kategori?->nama_kategori ?? '-' }}</small>
            </div>
            <div class="col-6">
                <div class="text-muted small fw-semibold text-uppercase">Lokasi Fisik Gudang</div>
                <div class="fw-bold fs-6 text-dark">{{ $opname->barang?->lokasi ?? 'Gudang Utama' }}</div>
                <small class="text-muted">Audit Record ID: #{{ $opname->id }}</small>
            </div>
        </div>

        <!-- Tabel Rekonsiliasi Audit -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="py-2">Parameter Audit</th>
                        <th class="text-end py-2">Kuantitas Unit</th>
                        <th class="py-2">Status & Hasil Rekonsiliasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2.5">Stok Sebelum Opname (Sistem)</td>
                        <td class="text-end py-2.5 fw-semibold text-secondary">{{ number_format($stokSistem) }} Unit</td>
                        <td class="py-2.5 text-muted small">Catatan sistem sebelum audit</td>
                    </tr>
                    <tr>
                        <td class="py-2.5"><strong>Hasil Perhitungan Fisik Riil (Aktual)</strong></td>
                        <td class="text-end py-2.5 fw-bold text-dark fs-6">{{ number_format($opname->stok_fisik) }} Unit</td>
                        <td class="py-2.5"><span class="badge bg-primary text-white">Hasil Audit Aktual Lapangan</span></td>
                    </tr>
                    <tr>
                        <td class="py-2.5"><strong>Selisih Stok (Discrepancy)</strong></td>
                        <td class="text-end py-2.5 fw-bold fs-5 {{ $opname->selisih > 0 ? 'text-success' : ($opname->selisih < 0 ? 'text-danger' : 'text-secondary') }}">
                            {{ $opname->selisih > 0 ? '+' : '' }}{{ number_format($opname->selisih) }} Unit
                        </td>
                        <td class="py-2.5">
                            @if ($opname->selisih > 0)
                                <span class="badge bg-success text-white">Surplus Fisik (+{{ $opname->selisih }})</span>
                            @elseif ($opname->selisih < 0)
                                <span class="badge bg-danger text-white">Defisit Fisik ({{ $opname->selisih }})</span>
                            @else
                                <span class="badge bg-secondary text-white">Stok 100% Sesuai</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Penjelasan Tindakan Rekonsiliasi Lot FIFO -->
        <div class="p-3 bg-light rounded-3 border mb-5">
            <h6 class="fw-bold text-dark mb-1 small text-uppercase">
                <i class="bi bi-info-circle-fill text-primary me-1"></i> Tindakan Rekonsiliasi Lot FIFO Otomatis:
            </h6>
            <p class="mb-0 small text-secondary">
                @if ($opname->selisih > 0)
                    Sistem otomatis menginisialisasi <strong>Lot Masuk Penyesuaian Opname</strong> sebanyak <strong>{{ $opname->selisih }} unit</strong> pada tanggal audit agar pool persediaan FIFO sinkron dengan stok fisik.
                @elseif ($opname->selisih < 0)
                    Sistem otomatis memotong saldo <strong>{{ abs($opname->selisih) }} unit</strong> dari <strong>Lot FIFO Aktif Tertua</strong> yang masih memiliki saldo untuk menjaga integritas alokasi pengeluaran.
                @else
                    Tidak ada penyesuaian lot yang diperlukan karena stok fisik identik dengan saldo tercatat pada sistem.
                @endif
            </p>
        </div>

        <!-- Kolom Tanda Tangan -->
        <div class="row g-4 pt-4">
            <div class="col-6">
                <div class="text-center pt-5 border-top border-dark">
                    <div class="fw-bold text-dark fs-6">Petugas Auditor Fisik</div>
                    <div class="text-muted small">Pelaksana Perhitungan Lapangan</div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center pt-5 border-top border-dark">
                    <div class="fw-bold text-dark fs-6">Kepala Gudang / Supervisor</div>
                    <div class="text-muted small">Verifikasi & Otorisasi Audit</div>
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
