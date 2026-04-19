@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Laporan Transaksi</h2>

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem;">Filter Laporan</h3>
    <form action="{{ route('inventory.laporan.transaksi') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="dari_tanggal">Dari Tanggal *</label>
            <input type="date" id="dari_tanggal" name="dari_tanggal" value="{{ $dari_tanggal }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="sampai_tanggal">Sampai Tanggal *</label>
            <input type="date" id="sampai_tanggal" name="sampai_tanggal" value="{{ $sampai_tanggal }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="tipe_transaksi">Tipe Transaksi</label>
            <select id="tipe_transaksi" name="tipe_transaksi">
                <option value="semua" {{ $tipe_transaksi === 'semua' ? 'selected' : '' }}>Semua</option>
                <option value="masuk" {{ $tipe_transaksi === 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="keluar" {{ $tipe_transaksi === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-success" style="width: 100%;">Tampilkan Laporan</button>
        </div>
    </form>
</div>

@if ($data)
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                    <td>
                        <span style="background-color: {{ $item['tipe'] === 'Masuk' ? '#27ae60' : '#e74c3c' }}; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                            {{ $item['tipe'] }}
                        </span>
                    </td>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td style="text-align: right;">{{ abs($item['jumlah']) }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="card">
        <p>Tidak ada data transaksi untuk periode ini.</p>
    </div>
@endif
@endsection
