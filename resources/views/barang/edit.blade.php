@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Edit Barang</h2>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('inventory.barang.update', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama_barang">Nama Barang *</label>
            <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
            @error('nama_barang')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="kategori_id">Kategori *</label>
            <select id="kategori_id" name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id', $barang->kategori_id) == $kat->id ? 'selected' : '' }}>
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
            <input type="number" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required>
            @error('stok_minimum')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="lokasi">Lokasi (opsional)</label>
            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $barang->lokasi) }}">
            @error('lokasi')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Stok Saat Ini</label>
            <input type="number" value="{{ $barang->stok }}" disabled>
            <p style="font-size: 0.85rem; color: #7f8c8d; margin-top: 0.25rem;">
                *Stok tidak dapat diubah dari sini. Gunakan Barang Masuk/Keluar atau Stock Opname.
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('inventory.barang.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
