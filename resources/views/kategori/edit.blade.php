@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Edit Kategori</h2>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('inventory.kategori.update', $kategori->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama_kategori">Nama Kategori *</label>
            <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
            @error('nama_kategori')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('inventory.kategori.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
