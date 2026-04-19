@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Laporan Stok Barang</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Stok Minimum</th>
            <th>Status</th>
            <th>Lokasi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($barang as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item['nama_barang'] }}</td>
                <td>{{ $item['kategori'] }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $item['stok'] }}</td>
                <td style="text-align: right;">{{ $item['stok_minimum'] }}</td>
                <td>
                    <span style="background-color: {{ $item['status'] === 'Kurang' ? '#e74c3c' : '#27ae60' }}; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                        {{ $item['status'] }}
                    </span>
                </td>
                <td>{{ $item['lokasi'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada barang</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
