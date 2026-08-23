@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('inventory.barang.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0">Edit Informasi Barang</h3>
        </div>

        <div class="card-elevated p-4">
            <form action="{{ route('inventory.barang.update', $barang->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_barang" class="form-label fw-semibold small text-secondary">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                    @error('nama_barang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kategori_id" class="form-label fw-semibold small text-secondary">Kategori <span class="text-danger">*</span></label>
                    <select id="kategori_id" name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id', $barang->kategori_id) == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="stok_minimum" class="form-label fw-semibold small text-secondary">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" id="stok_minimum" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required>
                    @error('stok_minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label fw-semibold small text-secondary">Lokasi Rak / Gudang <small class="text-muted">(opsional)</small></label>
                    <input type="text" id="lokasi" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $barang->lokasi) }}">
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <label class="form-label fw-semibold small text-secondary mb-1">Stok Saat Ini</label>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4 fw-bold text-dark">{{ $barang->stok }}</span>
                        <span class="badge badge-subtle-info">Unit</span>
                    </div>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i> Stok diubah via menu <strong>Barang Masuk</strong>, <strong>Barang Keluar</strong>, atau <strong>Stock Opname</strong>.
                    </small>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('inventory.barang.index') }}" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
