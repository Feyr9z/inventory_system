<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #3B82F6;
            --bs-success: #10B981;
            --bs-danger: #EF4444;
            --bs-warning: #F59E0B;
            --bs-info: #06B6D4;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, var(--bs-primary) 0%, #1E40AF 100%);
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: white !important;
        }
        .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        .user-info {
            color: white;
            font-size: 0.9rem;
        }
        .user-name {
            font-weight: 600;
            margin-bottom: 0;
        }
        .user-role {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        main {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        .page-title {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .stat-card h5 {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--bs-primary);
        }
        .stat-card.success .stat-value {
            color: var(--bs-success);
        }
        .stat-card.danger .stat-value {
            color: var(--bs-danger);
        }
        .stat-card.warning .stat-value {
            color: var(--bs-warning);
        }
        .stat-card.info .stat-value {
            color: var(--bs-info);
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        table thead {
            background-color: #f3f4f6;
            border-bottom: 2px solid #e5e7eb;
        }
        table th {
            font-weight: 600;
            color: #374151;
            padding: 1rem;
            border: none;
        }
        table td {
            padding: 1rem;
            border-color: #f3f4f6;
        }
        table tbody tr {
            transition: background-color 0.2s ease;
        }
        table tbody tr:hover {
            background-color: #f9fafb;
        }
        .stock-alert {
            color: var(--bs-danger);
            font-weight: 600;
        }
        .btn-sm {
            padding: 0.35rem 0.6rem;
            font-size: 0.8rem;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #f9fafb;
            border-color: #e5e7eb;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @if (auth()->check())
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('inventory.dashboard') }}">
                    📦 Inventory
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('inventory.dashboard') }}">Dashboard</a>
                        </li>
                        
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.barang.index') }}">Barang</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.kategori.index') }}">Kategori</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.user.index') }}">User</a>
                            </li>
                        @endif
                        
                        @if (in_array(auth()->user()->role, ['admin', 'staff']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown">
                                    Transaksi
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('inventory.transaksi.masuk.create') }}">📥 Barang Masuk</a></li>
                                    <li><a class="dropdown-item" href="{{ route('inventory.transaksi.keluar.create') }}">📤 Barang Keluar</a></li>
                                    @if (auth()->user()->role === 'admin')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('inventory.transaksi.opname.create') }}">🔍 Stock Opname</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        
                        @if (in_array(auth()->user()->role, ['admin', 'management']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown">
                                    Laporan
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('inventory.laporan.transaksi') }}">📈 Laporan Transaksi</a></li>
                                    <li><a class="dropdown-item" href="{{ route('inventory.laporan.stok') }}">📊 Laporan Stok</a></li>
                                </ul>
                            </li>
                        @endif
                        
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.log-aktivitas') }}">📝 Log</a>
                            </li>
                        @endif
                        
                        @if (in_array(auth()->user()->role, ['staff', 'management']))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.barang.index') }}">📦 Stok</a>
                            </li>
                        @endif
                    </ul>
                    <div class="d-flex align-items-center">
                        <div class="user-info me-3">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main>
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Terjadi Kesalahan!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✓ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ✗ {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
