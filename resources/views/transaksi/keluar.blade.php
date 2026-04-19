@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Barang Keluar</h2>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('inventory.transaksi.keluar.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="barang_id">Barang *</label>
            <select id="barang_id" name="barang_id" required>
                <option value="">-- Pilih Barang --</option>
                @foreach ($barang as $item)
                    <option value="{{ $item->id }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }} (Stok: {{ $item->stok }})
                    </option>
                @endforeach
            </select>
            @error('barang_id')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah *</label>
            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1" required>
            @error('jumlah')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal *</label>
            <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            @error('tanggal')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="tujuan">Tujuan *</label>
            <input type="text" id="tujuan" name="tujuan" value="{{ old('tujuan') }}" placeholder="Misal: Departemen A, Customer XYZ" required>
            @error('tujuan')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
