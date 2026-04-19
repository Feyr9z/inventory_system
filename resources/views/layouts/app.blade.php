<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Inventory Management</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <div class="header-top">
            <div class="logo-section">
                <h1>📦 Inventory</h1>
                <small>Management System</small>
            </div>
            
            @if (auth()->check())
                <div class="user-section">
                    <div class="user-info">
                        <p class="user-name">{{ auth()->user()->name }}</p>
                        <p class="user-role">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            @endif
        </div>
        
        @if (auth()->check())
            <nav>
                <a href="{{ route('inventory.dashboard') }}">Dashboard</a>
                
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('inventory.barang.index') }}">Barang</a>
                    <a href="{{ route('inventory.kategori.index') }}">Kategori</a>
                    <a href="{{ route('inventory.user.index') }}">User</a>
                @endif
                
                @if (in_array(auth()->user()->role, ['admin', 'staff']))
                    <a href="{{ route('inventory.transaksi.masuk.create') }}">Masuk</a>
                    <a href="{{ route('inventory.transaksi.keluar.create') }}">Keluar</a>
                @endif
                
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('inventory.transaksi.opname.create') }}">Opname</a>
                @endif
                
                @if (in_array(auth()->user()->role, ['admin', 'management']))
                    <a href="{{ route('inventory.laporan.transaksi') }}">Laporan</a>
                @endif
                
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('inventory.log-aktivitas') }}">Log</a>
                @endif
                
                @if (in_array(auth()->user()->role, ['staff', 'management']))
                    <a href="{{ route('inventory.stok') }}">Stok</a>
                @endif
            </nav>
        @endif
    </header>

    <main>
        <div class="container">
            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
