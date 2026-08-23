@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('inventory.user.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0">Edit Account User</h3>
        </div>

        <div class="card-elevated p-4">
            <form action="{{ route('inventory.user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" id="name" name="name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small text-secondary">Password Baru <small class="text-muted">(kosongkan jika tidak ingin diubah)</small></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="password" name="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-muted"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control border-start-0 ps-0" placeholder="••••••••">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="role" class="form-label fw-semibold small text-secondary">Role Hak Akses <span class="text-danger">*</span></label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('inventory.user.index') }}" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
