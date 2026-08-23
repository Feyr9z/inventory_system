@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0">Input Stock Opname</h3>
        </div>

        <div class="card-elevated p-4">
            <form action="{{ route('inventory.transaksi.opname.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="barang_id" class="form-label fw-semibold small text-secondary">Pilih Barang <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-box-seam text-muted"></i></span>
                        <select id="barang_id" name="barang_id" class="form-select border-start-0 ps-0 @error('barang_id') is-invalid @enderror" required onchange="updateStokSistem()">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->id }}" data-stok="{{ $item->stok }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }} (Stok Sistem: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('barang_id')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Stok Sistem</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-pc-display text-muted"></i></span>
                            <input type="number" id="stok_sistem" class="form-control border-start-0 ps-0 bg-light" disabled placeholder="0">
                        </div>
                        <small class="text-muted fs-7">Jumlah tercatat di database</small>
                    </div>
                    <div class="col-md-6">
                        <label for="stok_fisik" class="form-label fw-semibold small text-secondary">Stok Fisik (Hasil Hitung) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-clipboard-check text-muted"></i></span>
                            <input type="number" id="stok_fisik" name="stok_fisik" class="form-control border-start-0 ps-0 @error('stok_fisik') is-invalid @enderror" value="{{ old('stok_fisik') }}" min="0" required onchange="updateSelisih()" onkeyup="updateSelisih()" placeholder="0">
                        </div>
                        @error('stok_fisik')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Selisih Stok</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calculator text-muted"></i></span>
                        <input type="number" id="selisih" class="form-control border-start-0 ps-0 fw-bold" disabled placeholder="0">
                    </div>
                    <small class="text-muted fs-7">Selisih = Stok Fisik - Stok Sistem</small>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label fw-semibold small text-secondary">Tanggal Opname <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                        <input type="date" id="tanggal" name="tanggal" class="form-control border-start-0 ps-0 @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    </div>
                    @error('tanggal')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('inventory.dashboard') }}" class="btn-app-secondary">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn-app-primary">
                        <i class="bi bi-check-lg"></i> Simpan Stock Opname
                    </button>
                </div>
            </form>
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
        const selisihInput = document.getElementById('selisih');
        selisihInput.value = selisih;
        if (selisih > 0) {
            selisihInput.className = 'form-control border-start-0 ps-0 fw-bold text-success';
        } else if (selisih < 0) {
            selisihInput.className = 'form-control border-start-0 ps-0 fw-bold text-danger';
        } else {
            selisihInput.className = 'form-control border-start-0 ps-0 fw-bold text-dark';
        }
    }

    document.addEventListener('DOMContentLoaded', updateStokSistem);
</script>
@endsection
