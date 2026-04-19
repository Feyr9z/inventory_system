@extends('layouts.app')

@section('title', 'History Stock Opname')

@section('content')
<div class="mb-4">
    <h2 class="page-title">History Stock Opname</h2>
    <p class="text-muted">Riwayat semua stock opname yang telah dilakukan. Lihat perubahan stok dan selisih dari setiap opname.</p>
</div>

@if ($opname->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th class="text-end">Stok Fisik</th>
                    <th class="text-end">Selisih</th>
                    <th>Status Selisih</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($opname as $item)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</strong></td>
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td class="text-end"><strong>{{ $item->stok_fisik }}</strong></td>
                        <td class="text-end">
                            <span style="color: {{ $item->selisih > 0 ? '#10b981' : ($item->selisih < 0 ? '#ef4444' : '#6b7280') }}; font-weight: bold;">
                                {{ $item->selisih > 0 ? '+' : '' }}{{ $item->selisih }}
                            </span>
                        </td>
                        <td>
                            @if ($item->selisih > 0)
                                <span class="badge bg-success">Tambah ({{$item->selisih}})</span>
                            @elseif ($item->selisih < 0)
                                <span class="badge bg-danger">Kurang ({{$item->selisih}})</span>
                            @else
                                <span class="badge bg-secondary">Sesuai</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            {{ $opname->links() }}
        </ul>
    </nav>

    <div class="mt-3 text-muted">
        <small>📌 <strong>Penjelasan:</strong> Selisih = Stok Fisik - Stok Sistem | (+) Lebih = ada barang yg tidak tercatat | (-) Kurang = ada barang yg hilang/rusak | (=) Sesuai = stok sudah akurat</small>
    </div>
@else
    <div class="alert alert-info" role="alert">
        <strong>Belum ada history stock opname.</strong> Mulai dengan melakukan stock opname pertama kali.
    </div>
@endif

<div class="mt-4">
    <a href="{{ route('inventory.transaksi.opname.create') }}" class="btn btn-primary">
        🔍 Lakukan Stock Opname Baru
    </a>
</div>
@endsection
