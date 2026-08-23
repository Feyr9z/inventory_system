@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('inventory.kategori.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0">Tambah Kategori</h3>
        </div>

        <div class="card-elevated p-4">
            <form action="{{ route('inventory.kategori.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="nama_kategori" class="form-label fw-semibold small text-secondary">Nama Kategori <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-tag text-muted"></i></span>
                        <input type="text" id="nama_kategori" name="nama_kategori" class="form-control border-start-0 ps-0 @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori') }}" placeholder="Contoh: Elektronik, Bahan Baku" required>
                    </div>
                    @error('nama_kategori')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('inventory.kategori.index') }}" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-check-lg"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
