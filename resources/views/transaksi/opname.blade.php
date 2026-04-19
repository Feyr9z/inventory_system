@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 class="page-title">🔍 Stock Opname</h2>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('inventory.transaksi.opname.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                        <select id="barang_id" name="barang_id" class="form-select @error('barang_id') is-invalid @enderror" required onchange="updateStokSistem()">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->id }}" data-stok="{{ $item->stok }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }} (Stok Sistem: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok Sistem</label>
                        <input type="number" id="stok_sistem" class="form-control" disabled>
                        <small class="text-muted d-block mt-2">Stok yang tercatat di sistem</small>
                    </div>

                    <div class="mb-3">
                        <label for="stok_fisik" class="form-label">Stok Fisik (Hasil Hitung) <span class="text-danger">*</span></label>
                        <input type="number" id="stok_fisik" name="stok_fisik" class="form-control @error('stok_fisik') is-invalid @enderror" value="{{ old('stok_fisik') }}" min="0" required onchange="updateSelisih()">
                        @error('stok_fisik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selisih</label>
                        <input type="number" id="selisih" class="form-control" disabled>
                        <small class="text-muted d-block mt-2">Selisih = Stok Fisik - Stok Sistem</small>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary">✓ Simpan</button>
                        <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
