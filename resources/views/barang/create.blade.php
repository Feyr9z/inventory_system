@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Tambah Barang Baru</h2>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('inventory.barang.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nama_barang">Nama Barang *</label>
            <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required>
            @error('nama_barang')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="kategori_id">Kategori *</label>
            <select id="kategori_id" name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            @error('kategori_id')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="stok_minimum">Stok Minimum *</label>
            <input type="number" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', 0) }}" min="0" required>
            @error('stok_minimum')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="lokasi">Lokasi (opsional)</label>
            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}">
            @error('lokasi')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('inventory.barang.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
