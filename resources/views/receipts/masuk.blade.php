<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Bukti Penerimaan Barang - {{ $docNumber }}</title>
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
                        <i class="bi bi-box-seam-fill fs-5"></i>
                    </div>
                    <span class="fw-bold fs-4 text-dark tracking-tight">PT ATHA ANAKHATULISTIWA</span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase letter-spacing-1">
                    Surat Bukti Penerimaan Barang (Inbound Goods Receipt)
                </div>
            </div>
            <div class="text-end">
                <div class="font-monospace fw-bold fs-5 text-dark">{{ $docNumber }}</div>
                <div class="text-muted small">Tanggal: <strong>{{ $masuk->tanggal->format('d/m/Y') }}</strong></div>
                <div class="text-muted small">Waktu Input: {{ $masuk->created_at ? $masuk->created_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>

        <!-- Metadata Transaksi -->
        <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
            <div class="col-6">
                <div class="text-muted small fw-semibold text-uppercase">Petugas Penerima Gudang</div>
                <div class="fw-bold fs-6 text-dark">{{ $masuk->user?->name ?? 'Staff Gudang' }}</div>
                <small class="text-muted">Peran: {{ $masuk->user ? (\App\Enums\Role::tryFrom($masuk->user->role)?->label() ?? ucfirst($masuk->user->role)) : '-' }}</small>
            </div>
            <div class="col-6">
                <div class="text-muted small fw-semibold text-uppercase">Pemasok / Sumber Barang</div>
                <div class="fw-bold fs-6 text-dark">{{ $masuk->sumber }}</div>
                <small class="text-muted">Lokasi Penyimpanan: {{ $masuk->barang?->lokasi ?? 'Gudang Utama' }}</small>
            </div>
        </div>

        <!-- Tabel Barang Diterima -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="py-2">Nama Barang</th>
                        <th class="py-2">Kategori</th>
                        <th class="text-end py-2">Jumlah Unit</th>
                        <th class="py-2">Status Inisialisasi Lot FIFO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-3">
                            <strong class="text-dark fs-6">{{ $masuk->barang?->nama_barang ?? 'Barang #' . $masuk->barang_id }}</strong>
                        </td>
                        <td class="py-3 text-muted">{{ $masuk->barang?->kategori?->nama_kategori ?? '-' }}</td>
                        <td class="py-3 text-end">
                            <span class="fw-bold fs-5 text-success">+{{ number_format($masuk->jumlah) }}</span> <span class="small text-muted">Unit</span>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-success text-white">Lot Inisialisasi Berhasil</span>
                            <div class="small text-muted font-monospace mt-1">ID Lot Pool: #{{ $masuk->id }} (Sisa: {{ number_format($masuk->sisa_jumlah) }} unit)</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Catatan FIFO Pool -->
        <div class="p-3 bg-light rounded-3 border mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted text-uppercase fw-semibold d-block">Identitas Lot Persediaan (FIFO Pool)</span>
                    <span class="font-monospace fw-bold text-dark fs-6">Lot ID #{{ $masuk->id }}</span>
                </div>
                <div class="text-end">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Saldo Sisa Lot Saat Ini</span>
                    <span class="fw-bold text-success fs-6">{{ number_format($masuk->sisa_jumlah) }} Unit</span>
                </div>
            </div>
        </div>

        <!-- Kolom Tanda Tangan -->
        <div class="row g-4 pt-4">
            <div class="col-6">
                <div class="text-center pt-5 border-top border-dark">
                    <div class="fw-bold text-dark fs-6">{{ $masuk->user?->name ?? 'Petugas Gudang' }}</div>
                    <div class="text-muted small">Petugas Penerima (Gudang)</div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center pt-5 border-top border-dark">
                    <div class="fw-bold text-dark fs-6">{{ $masuk->sumber }}</div>
                    <div class="text-muted small">Pengirim / Pemasok Ekspedisi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-trigger print if requested via query param ?autoprint=1
    if (window.location.search.includes('autoprint=1')) {
        window.print();
    }
</script>

</body>
</html>
