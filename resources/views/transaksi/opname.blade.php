@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<h2 style="color: #2c3e50; margin-bottom: 1rem;">Stock Opname</h2>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('inventory.transaksi.opname.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="barang_id">Barang *</label>
            <select id="barang_id" name="barang_id" required onchange="updateStokSistem()">
                <option value="">-- Pilih Barang --</option>
                @foreach ($barang as $item)
                    <option value="{{ $item->id }}" data-stok="{{ $item->stok }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }} (Stok Sistem: {{ $item->stok }})
                    </option>
                @endforeach
            </select>
            @error('barang_id')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Stok Sistem</label>
            <input type="number" id="stok_sistem" disabled>
            <p style="font-size: 0.85rem; color: #7f8c8d; margin-top: 0.25rem;">
                Stok yang tercatat di sistem
            </p>
        </div>

        <div class="form-group">
            <label for="stok_fisik">Stok Fisik (Hasil Hitung) *</label>
            <input type="number" id="stok_fisik" name="stok_fisik" value="{{ old('stok_fisik') }}" min="0" required onchange="updateSelisih()">
            @error('stok_fisik')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Selisih</label>
            <input type="number" id="selisih" disabled>
            <p style="font-size: 0.85rem; color: #7f8c8d; margin-top: 0.25rem;">
                Selisih = Stok Fisik - Stok Sistem
            </p>
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal *</label>
            <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            @error('tanggal')
                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
    function updateStokSistem() {
        const select = document.getElementById('barang_id');
        const stokInput = document.getElementById('stok_sistem');
        const option = select.options[select.selectedIndex];
        stokInput.value = option.dataset.stok || '';
        updateSelisih();
    }

    function updateSelisih() {
        const stokSistem = parseFloat(document.getElementById('stok_sistem').value) || 0;
        const stokFisik = parseFloat(document.getElementById('stok_fisik').value) || 0;
        const selisih = stokFisik - stokSistem;
        document.getElementById('selisih').value = selisih;
    }

    // Initial call
    document.addEventListener('DOMContentLoaded', updateStokSistem);
</script>
@endsection
