@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 class="page-title">Edit Barang</h2>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('inventory.barang.update', $barang->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
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
                        <label for="stok_minimum" class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                        <input type="number" id="stok_minimum" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required>
                        @error('stok_minimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi <small class="text-muted">(opsional)</small></label>
                        <input type="text" id="lokasi" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $barang->lokasi) }}">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="number" class="form-control" value="{{ $barang->stok }}" disabled>
                        <small class="text-muted d-block mt-2">
                            💡 Stok tidak dapat diubah dari sini. Gunakan <strong>Barang Masuk</strong>, <strong>Barang Keluar</strong>, atau <strong>Stock Opname</strong> untuk mengubah stok.
                        </small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary">✓ Simpan</button>
                        <a href="{{ route('inventory.barang.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
