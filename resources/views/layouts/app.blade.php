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

                        @if (auth()->user()->role === 'admin')
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
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.user.*') ? 'active' : '' }}" href="{{ route('inventory.user.index') }}">
                                    User
                                </a>
                            </li>
                        @endif

                        @if (in_array(auth()->user()->role, ['admin', 'staff']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('inventory.transaksi.*') ? 'active' : '' }}" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Transaksi
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="transaksiDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.transaksi.masuk.create') }}">
                                            Barang Masuk
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.transaksi.keluar.create') }}">
                                            Barang Keluar
                                        </a>
                                    </li>
                                    @if (auth()->user()->role === 'admin')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.opname.create') }}">
                                                Stock Opname
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inventory.transaksi.opname.history') }}">
                                                History Opname
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (in_array(auth()->user()->role, ['admin', 'management']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('inventory.laporan.*') ? 'active' : '' }}" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Laporan
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="laporanDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.laporan.transaksi') }}">
                                            Laporan Transaksi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.laporan.stok') }}">
                                            Laporan Stok
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.transaksi.opname.history') }}">
                                            History Opname
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (in_array(auth()->user()->role, ['admin', 'management']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.log-aktivitas') ? 'active' : '' }}" href="{{ route('inventory.log-aktivitas') }}">
                                    Log Aktivitas
                                </a>
                            </li>
                        @endif

                        @if (in_array(auth()->user()->role, ['staff', 'management']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('inventory.barang.*') ? 'active' : '' }}" href="{{ route('inventory.barang.index') }}">
                                    Stok Barang
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="navbar-user-section d-flex align-items-center justify-content-between justify-content-lg-end gap-3">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <div class="avatar-initial">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold lh-1 fs-6 text-white">{{ auth()->user()->name }}</div>
                                <small class="text-white-50 text-capitalize fs-7">{{ auth()->user()->role }}</small>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 rounded-2">
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
                <div class="alert alert-danger alert-custom alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 flex-shrink-0"></i>
                    <div>
                        <strong class="d-block fw-semibold mb-1">Terjadi Kesalahan!</strong>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-custom alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-custom alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-octagon-fill fs-5 flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
