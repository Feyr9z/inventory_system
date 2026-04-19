@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<h2 class="page-title mb-4">Laporan Stok Barang</h2>

@if (count($barang) > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-end">Stok</th>
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
                        <td class="text-end"><strong>{{ $item['stok'] }}</strong></td>
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
@else
    <div class="alert alert-info" role="alert">
        <strong>Belum ada barang.</strong>
    </div>
@endif
@endsection
