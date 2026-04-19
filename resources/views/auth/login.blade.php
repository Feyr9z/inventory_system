<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }
        .login-box {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 0;
        }
        .form-control {
            border-color: #e5e7eb;
            border-radius: 6px;
            padding: 0.65rem 0.875rem;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .form-label {
            color: #374151;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .form-check-input {
            border-color: #d1d5db;
        }
        .form-check-input:checked {
            background-color: #3B82F6;
            border-color: #3B82F6;
        }
        .btn-login {
            background: linear-gradient(135deg, #3B82F6 0%, #2563eb 100%);
            border: none;
            padding: 0.75rem 1rem;
            font-weight: 600;
            border-radius: 6px;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            margin-top: 1.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .error-message {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        .error-message ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        .error-message li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>Atha Inventory</h1>
                <p>Management System</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required value="{{ old('email') }}" placeholder="admin@inventory.test">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
