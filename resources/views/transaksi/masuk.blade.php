@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 class="page-title">📥 Input Barang Masuk</h2>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('inventory.transaksi.masuk.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                        <select id="barang_id" name="barang_id" class="form-select @error('barang_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->id }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }} (Stok: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" id="jumlah" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" min="1" required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sumber" class="form-label">Sumber <span class="text-danger">*</span></label>
                        <input type="text" id="sumber" name="sumber" class="form-control @error('sumber') is-invalid @enderror" value="{{ old('sumber') }}" placeholder="Misal: Supplier A, Gudang Pusat" required>
                        @error('sumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary">✓ Simpan</button>
                        <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
