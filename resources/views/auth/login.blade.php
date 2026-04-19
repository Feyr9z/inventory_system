<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>📦 Inventory</h1>
                <p>Management System</p>
            </div>

            <div class="login-form">
                @if ($errors->any())
                    <div class="errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="demo-users">
                        <h4>Demo Users</h4>
                        <div class="demo-user">
                            <strong>👤 Admin</strong>
                            <code>admin@inventory.test / password</code>
                        </div>
                        <div class="demo-user">
                            <strong>👤 Staff</strong>
                            <code>staff@inventory.test / password</code>
                        </div>
                        <div class="demo-user">
                            <strong>👤 Management</strong>
                            <code>management@inventory.test / password</code>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>

                    <div class="form-group">
                        <label for="remember">
                            <input type="checkbox" id="remember" name="remember" style="width: auto; margin-right: 0.5rem;">
                            <span>Remember Me</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
