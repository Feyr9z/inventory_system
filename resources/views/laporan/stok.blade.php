@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="mb-4">
    <h2 class="page-title">Laporan Stok Barang</h2>
    <p class="text-muted">Pantau kondisi stok barang saat ini. Status "Kurang" menunjukkan stok di bawah minimum yang telah ditentukan.</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Filter Laporan</h5>
        <form action="{{ route('inventory.laporan.stok') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="kategori_id" class="form-label">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->id }}" {{ $kategori_id == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status Stok</label>
                <select id="status" name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="normal" {{ $status == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="kurang" {{ $status == 'kurang' ? 'selected' : '' }}>Kurang</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">🔍 Filter</button>
                @if (count($barang) > 0)
                    <a href="{{ route('inventory.laporan.stok.export') }}?kategori_id={{ $kategori_id }}&status={{ $status }}" class="btn btn-success" title="Download as CSV">
                        📥 CSV
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if (count($barang) > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-end">Stok Saat Ini</th>
                    <th class="text-end">Stok Minimum</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $idx => $item)
                    <tr>
                        <td><small class="text-muted">#{{ $idx + 1 }}</small></td>
                        <td><strong>{{ $item['nama_barang'] }}</strong></td>
                        <td><span class="badge bg-info">{{ $item['kategori'] }}</span></td>
                        <td class="text-end"><strong style="color: {{ $item['stok'] < $item['stok_minimum'] ? '#ef4444' : '#10b981' }}">{{ $item['stok'] }}</strong></td>
                        <td class="text-end">{{ $item['stok_minimum'] }}</td>
                        <td>
                            <span class="badge {{ $item['status'] === 'Kurang' ? 'bg-danger' : 'bg-success' }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td><small>{{ $item['lokasi'] ?? '-' }}</small></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($barang->hasPages())
        <nav aria-label="Page navigation" class="mt-4">
            {{ $barang->appends(request()->query())->links('pagination::bootstrap-5') }}
        </nav>
    @endif

    <div class="mt-3 text-muted">
        <small>📌 <strong>Penjelasan:</strong> Stok Saat Ini = jumlah barang yang tersedia | Stok Minimum = batas terendah yang disarankan | Status Kurang = perlu pemesanan barang baru</small>
    </div>
@else
    <div class="alert alert-info" role="alert">
        <strong>Tidak ada barang.</strong>
    </div>
@endif
@endsection
