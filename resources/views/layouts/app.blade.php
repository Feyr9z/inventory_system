<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Inventory Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        nav {
            margin-top: 0.5rem;
        }

        nav a {
            color: #ecf0f1;
            text-decoration: none;
            margin-right: 1.5rem;
            font-size: 0.9rem;
        }

        nav a:hover {
            color: #3498db;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-secondary {
            background-color: #95a5a6;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #229954;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: bold;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            font-family: Arial, sans-serif;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        th {
            background-color: #34495e;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #ecf0f1;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .errors {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .errors ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .errors li {
            margin-bottom: 0.25rem;
        }

        @media (max-width: 600px) {
            .container {
                padding: 1rem;
            }

            header {
                padding: 1rem;
            }

            nav a {
                display: block;
                margin-bottom: 0.5rem;
                margin-right: 0;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 0.5rem;
            }

            .btn {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>📦 Inventory Management System</h1>
        <nav>
            <a href="{{ route('inventory.dashboard') }}">Dashboard</a>
            <a href="{{ route('inventory.barang.index') }}">Barang</a>
            <a href="{{ route('inventory.transaksi.masuk.create') }}">Barang Masuk</a>
            <a href="{{ route('inventory.transaksi.keluar.create') }}">Barang Keluar</a>
            <a href="{{ route('inventory.transaksi.opname.create') }}">Stock Opname</a>
        </nav>
    </header>

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
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
