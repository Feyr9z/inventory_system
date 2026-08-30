<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'Inventory Management') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- App Asset Bundler -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @if (auth()->check())
        @php $userRole = auth()->user()->role; @endphp
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
            <div class="container-fluid px-lg-4">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inventory.dashboard') }}">
                    <div class="bg-primary text-white rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-box-seam-fill fs-6"></i>
                    </div>
                    <span>{{ config('app.name', 'INVENTORY') }}</span>
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-lg-3 gap-lg-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}" href="{{ route('inventory.dashboard') }}">
                                Dashboard
                            </a>
                        </li>

                        {{-- Barang: Admin + Kepala Gudang (full menu), Staff + Management (hanya lihat stok) --}}
                        @if (in_array($userRole, ['admin', 'kepala_gudang']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.barang.*') ? 'active' : '' }}" href="{{ route('inventory.barang.index') }}">
                                    Barang
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.kategori.*') ? 'active' : '' }}" href="{{ route('inventory.kategori.index') }}">
                                    Kategori
                                </a>
                            </li>
                        @endif

                        @if ($userRole === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.user.*') ? 'active' : '' }}" href="{{ route('inventory.user.index') }}">
                                    User
                                </a>
                            </li>
                        @endif

                        {{-- Stok Barang: Staff + Management (view-only label) --}}
                        @if (in_array($userRole, ['staff', 'management']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.barang.*') ? 'active' : '' }}" href="{{ route('inventory.barang.index') }}">
                                    Stok Barang
                                </a>
                            </li>
                        @endif

                        {{-- Transaksi: Admin + Staff (input); Kepala Gudang (opname saja) --}}
                        @if (in_array($userRole, ['admin', 'staff', 'kepala_gudang']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('inventory.transaksi.*') ? 'active' : '' }}" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Transaksi
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="transaksiDropdown">
                                    @if (in_array($userRole, ['admin', 'staff']))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.masuk.create') }}">
                                                <i class="bi bi-arrow-down-left-circle me-2 text-info"></i>Barang Masuk
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.keluar.create') }}">
                                                <i class="bi bi-arrow-up-right-circle me-2 text-warning"></i>Barang Keluar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.saya') }}">
                                                <i class="bi bi-clock-history me-2 text-primary"></i>Transaksi Saya
                                            </a>
                                        </li>
                                    @endif
                                    @if (in_array($userRole, ['admin', 'kepala_gudang']))
                                        @if (in_array($userRole, ['admin']))
                                            <li><hr class="dropdown-divider"></li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.opname.create') }}">
                                                <i class="bi bi-clipboard-check me-2 text-purple"></i>Stock Opname
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.opname.history') }}">
                                                <i class="bi bi-journal-text me-2 text-secondary"></i>History Opname
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- Transaksi Saya Nav Link khusus Staff --}}
                        @if ($userRole === 'staff')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.transaksi.saya') ? 'active' : '' }}" href="{{ route('inventory.transaksi.saya') }}">
                                    <i class="bi bi-clock-history me-1"></i> Transaksi Saya
                                </a>
                            </li>
                        @endif

                        {{-- Laporan: Admin + Kepala Gudang + Management --}}
                        @if (in_array($userRole, ['admin', 'kepala_gudang', 'management']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('inventory.laporan.*') ? 'active' : '' }}" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Laporan
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="laporanDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.laporan.transaksi') }}">
                                            <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Laporan Transaksi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.laporan.stok') }}">
                                            <i class="bi bi-bar-chart-line me-2 text-success"></i>Laporan Stok
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.transaksi.opname.history') }}">
                                            <i class="bi bi-journal-text me-2 text-secondary"></i>History Opname
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.log-aktivitas') ? 'active' : '' }}" href="{{ route('inventory.log-aktivitas') }}">
                                    Log Aktivitas
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="navbar-user-section d-flex align-items-center justify-content-between justify-content-lg-end gap-3">
                        <div class="user-profile-badge d-flex align-items-center gap-2">
                            <div class="avatar-initial" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="pe-1">
                                <div class="fw-semibold lh-1 fs-6 text-white">{{ auth()->user()->name }}</div>
                                <small class="text-white-50 fs-7">
                                    {{ \App\Enums\Role::tryFrom($userRole)?->label() ?? ucfirst($userRole) }}
                                </small>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-logout-custom">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main class="py-4">
        <div class="container-fluid px-lg-4">
            @if ($errors->any())
                <div class="alert alert-danger alert-custom alert-dismissible fade show d-flex align-items-start gap-3 mb-4" role="alert">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger text-white flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.9rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong class="d-block fw-semibold mb-1" style="font-size: 0.925rem;">Terjadi Kesalahan Validasi</strong>
                        @if ($errors->count() === 1)
                            <div class="small">{{ $errors->first() }}</div>
                        @else
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-custom alert-dismissible fade show d-flex align-items-center gap-3 mb-4" role="alert">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.9rem;">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="flex-grow-1 fw-semibold small">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-custom alert-dismissible fade show d-flex align-items-center gap-3 mb-4" role="alert">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger text-white flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.9rem;">
                        <i class="bi bi-x-lg"></i>
                    </div>
                    <div class="flex-grow-1 fw-semibold small">{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
