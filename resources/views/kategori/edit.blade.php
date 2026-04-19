@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 class="page-title">Edit Kategori</h2>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('inventory.kategori.update', $kategori->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" id="nama_kategori" name="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary">✓ Simpan</button>
                        <a href="{{ route('inventory.kategori.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
