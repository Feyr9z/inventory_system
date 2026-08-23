@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0">Input Barang Masuk</h3>
        </div>

        <div class="card-elevated p-4">
            <form action="{{ route('inventory.transaksi.masuk.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="barang_id" class="form-label fw-semibold small text-secondary">Pilih Barang <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-box-seam text-muted"></i></span>
                        <select id="barang_id" name="barang_id" class="form-select border-start-0 ps-0 @error('barang_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->id }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }} (Stok Saat Ini: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('barang_id')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label fw-semibold small text-secondary">Jumlah Unit Masuk <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-plus-circle text-muted"></i></span>
                        <input type="number" id="jumlah" name="jumlah" class="form-control border-start-0 ps-0 @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" min="1" placeholder="Masukkan jumlah unit" required>
                    </div>
                    @error('jumlah')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label fw-semibold small text-secondary">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                        <input type="date" id="tanggal" name="tanggal" class="form-control border-start-0 ps-0 @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    </div>
                    @error('tanggal')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="sumber" class="form-label fw-semibold small text-secondary">Sumber / Pemasok <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-truck text-muted"></i></span>
                        <input type="text" id="sumber" name="sumber" class="form-control border-start-0 ps-0 @error('sumber') is-invalid @enderror" value="{{ old('sumber') }}" placeholder="Contoh: PT Supplier Utama, Gudang Pusat" required>
                    </div>
                    @error('sumber')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-check-lg"></i> Simpan Barang Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
