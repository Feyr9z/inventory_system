<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan & Bukti Pengeluaran Barang - {{ $docNumber }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 16px;
            font-size: 13px;
            line-height: 1.5;
        }
        .print-wrapper {
            max-width: 820px;
            margin: 0 auto;
        }
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-close-window {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-close-window:hover {
            background-color: #e2e8f0;
        }
        .btn-print {
            background-color: #0f172a;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #1e293b;
        }
        .receipt-sheet {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 36px 42px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }
        .kop-surat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 3px double #0f172a;
        }
        .company-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
            margin: 0 0 2px 0;
        }
        .company-subtitle {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .company-address {
            font-size: 11px;
            color: #64748b;
        }
        .doc-title-bar {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 18px;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .doc-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-badge {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            background: #0f172a;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            padding: 14px 18px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .meta-sub {
            font-size: 11px;
            color: #64748b;
        }
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .doc-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            text-align: left;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-table td {
            padding: 12px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            vertical-align: top;
        }
        .badge-fifo {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #bae6fd;
        }
        .fifo-section {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .fifo-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .fifo-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        .fifo-table th {
            background-color: #e2e8f0;
            color: #334155;
            font-size: 11px;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .fifo-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 25px;
            margin-top: 36px;
            padding-top: 10px;
        }
        .signature-box {
            text-align: center;
            padding-top: 65px;
            border-top: 1px solid #0f172a;
        }
        .signature-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .signature-role {
            font-size: 11px;
            color: #64748b;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                font-size: 12px !important;
            }
            .no-print {
                display: none !important;
            }
            .print-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
            }
            .receipt-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            .doc-title-bar, .meta-grid, .fifo-section {
                background-color: #f8fafc !important;
                border: 1px solid #94a3b8 !important;
            }
            .doc-table th, .fifo-table th {
                background-color: #f1f5f9 !important;
                border: 1px solid #94a3b8 !important;
            }
            .doc-table td, .fifo-table td {
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</head>
<body>

<div class="print-wrapper">
    <!-- Action Bar (Hanya tampil di layar) -->
    <div class="no-print action-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="doc-badge">{{ $docNumber }}</span>
            <span style="color: #64748b; font-size: 12px;">Pratinjau Dokumen Siap Cetak (A4)</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" onclick="window.close()" class="btn btn-close-window">
                <i class="bi bi-x-lg"></i> Tutup
            </button>
            <button type="button" onclick="window.print()" class="btn btn-print">
                <i class="bi bi-printer-fill"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Resmi -->
    <div class="receipt-sheet">
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <div>
                <h1 class="company-title">PT ATHA ANAKHATULISTIWA</h1>
                <div class="company-subtitle">Divisi Manajemen Logistik & Inventoris Gudang</div>
                <div class="company-address">Jl. Raya Industri Grafika No. 88 • Telp: (021) 555-0199 • Email: logistic@atha.co.id</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600;">Tanggal Pengeluaran</div>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a;">{{ $keluar->tanggal->format('d/m/Y') }}</div>
                <div style="font-size: 11px; color: #64748b;">Waktu: {{ $keluar->created_at ? $keluar->created_at->format('H:i') : '-' }} WIB</div>
            </div>
        </div>

        <!-- BAR JUDUL DOKUMEN -->
        <div class="doc-title-bar">
            <div>
                <div class="doc-name">Surat Jalan & Bukti Pengeluaran Barang</div>
                <div style="font-size: 11px; color: #64748b;">Outbound Goods Issue & FIFO Allocation Slip</div>
            </div>
            <div class="doc-badge">{{ $docNumber }}</div>
        </div>

        <!-- METADATA TRANSAKSI -->
        <div class="meta-grid">
            <div>
                <div class="meta-label">Petugas Pengeluar Gudang</div>
                <div class="meta-value">{{ $keluar->user?->name ?? 'Staff Gudang' }}</div>
                <div class="meta-sub">Peran: {{ $keluar->user ? (\App\Enums\Role::tryFrom($keluar->user->role)?->label() ?? ucfirst($keluar->user->role)) : 'Staff' }}</div>
            </div>
            <div>
                <div class="meta-label">Tujuan / Keperluan / Penerima</div>
                <div class="meta-value">{{ $keluar->tujuan }}</div>
                <div class="meta-sub">Lokasi Asal: {{ $keluar->barang?->lokasi ?? 'Gudang Utama' }}</div>
            </div>
        </div>

        <!-- TABEL BARANG DIKELUARKAN -->
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Nama Barang</th>
                    <th style="width: 20%;">Kategori</th>
                    <th style="width: 15%; text-align: right;">Kuantitas</th>
                    <th style="width: 20%;">Metode Alokasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="color: #0f172a; font-size: 13px;">{{ $keluar->barang?->nama_barang ?? 'Barang #' . $keluar->barang_id }}</strong>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Lokasi Rak: {{ $keluar->barang?->lokasi ?? 'Gudang Utama' }}</div>
                    </td>
                    <td style="color: #475569;">{{ $keluar->barang?->kategori?->nama_kategori ?? '-' }}</td>
                    <td style="text-align: right;">
                        <span style="font-size: 15px; font-weight: 700; color: #b91c1c;">-{{ number_format($keluar->jumlah) }}</span>
                        <span style="font-size: 11px; color: #64748b; display: block;">Unit</span>
                    </td>
                    <td>
                        <span class="badge-fifo">Alokasi FIFO Terpenuhi</span>
                        <div style="font-size: 11px; color: #475569; margin-top: 3px;">{{ $keluar->details->count() }} Lot Terkonsumsi</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- RINCIAN KONSUMSI LOT FIFO -->
        <div class="fifo-section">
            <div class="fifo-title">
                <i class="bi bi-layers-fill" style="margin-right: 4px;"></i> Rincian Konsumsi Lot FIFO (First In First Out):
            </div>
            <table class="fifo-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Lot Reference</th>
                        <th style="width: 25%;">Tgl Masuk Lot Asal</th>
                        <th style="width: 35%;">Pemasok / Sumber Asal</th>
                        <th style="width: 20%; text-align: right;">Qty Diambil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($keluar->details as $detail)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">Lot #{{ $detail->barang_masuk_id }}</td>
                            <td>{{ $detail->barangMasuk?->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td style="color: #64748b;">{{ $detail->barangMasuk?->sumber ?? '-' }}</td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">{{ number_format($detail->jumlah_diambil) }} Unit</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8;">Tidak ada rincian alokasi lot</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- KOLOM TANDA TANGAN RESMI -->
        <div class="signature-grid">
            <div>
                <div class="signature-box">
                    <div class="signature-name">{{ $keluar->user?->name ?? 'Staff Gudang' }}</div>
                    <div class="signature-role">Petugas Pembuat Dokumen</div>
                </div>
            </div>
            <div>
                <div class="signature-box">
                    <div class="signature-name">{{ $keluar->approver?->name ?? 'Kepala Gudang' }}</div>
                    <div class="signature-role">Kepala Gudang (Otorisasi)</div>
                </div>
            </div>
            <div>
                <div class="signature-box">
                    <div class="signature-name">{{ $keluar->tujuan }}</div>
                    <div class="signature-role">Penerima Barang / Driver</div>
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
