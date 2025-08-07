<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - ARIDA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #2d2d2d;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            border: 1px solid #404040;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
            height: 80px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-color: transparent;
            border-radius: 8px;
            position: relative;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #404040 0%, #606060 100%);
            border-radius: 8px;
            font-size: 28px;
            font-weight: bold;
            color: #e0e0e0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .system-title {
            color: #e0e0e0;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .system-subtitle {
            color: #b0b0b0;
            font-size: 14px;
            line-height: 1.4;
        }

        .company-name {
            color: #888888;
            font-size: 12px;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #e0e0e0;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #404040;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            background: #1a1a1a;
            color: #e0e0e0;
        }

        .form-group input:focus {
            outline: none;
            border-color: #606060;
        }

        .form-group input::placeholder {
            color: #888888;
        }

        .form-group input.is-invalid {
            border-color: #e74c3c;
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #2d1b1b;
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }

        .alert-success {
            background-color: #1b2d1b;
            border: 1px solid #27ae60;
            color: #27ae60;
        }

        .login-btn {
            width: 100%;
            background: #404040;
            color: #e0e0e0;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: #505050;
        }

        .login-btn:active {
            transform: translateY(1px);
        }

        .login-btn:disabled {
            background: #2a2a2a;
            cursor: not-allowed;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #b0b0b0;
        }

        .register-link a {
            color: #b0b0b0;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
            color: #e0e0e0;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 15px;
                padding: 25px 20px;
            }

            .logo {
                width: 100px;
                height: 65px;
            }

            .logo-placeholder {
                font-size: 22px;
            }

            .system-title {
                font-size: 20px;
            }

            .system-subtitle {
                font-size: 12px;
            }

            .company-name {
                font-size: 11px;
            }

            .form-group input {
                padding: 10px 12px;
                font-size: 16px;
            }

            .login-btn {
                padding: 14px;
                font-size: 16px;
            }
        }

        @media (max-width: 360px) {
            .login-container {
                margin: 10px;
                padding: 20px 15px;
            }

            .logo {
                width: 90px;
                height: 60px;
            }

            .logo-placeholder {
                font-size: 18px;
            }

            .system-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo">
                <!-- Coba beberapa kemungkinan path logo -->
                <img src="{{ asset('images/logoarida.png') }}"
                     alt="Logo ARIDA"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <!-- Fallback placeholder logo -->
                <div class="logo-placeholder" style="display: none;">
                    ARIDA
                </div>
            </div>
            <h1 class="system-title">LOGIN</h1>
            <p class="system-subtitle">
                Sistem Penentuan Prioritas<br>
                Pemeliharaan Mesin Produksi Benang
            </p>
            <p class="company-name">PT. Arteria Daya Mulia</p>
        </div>

        {{-- Display error messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        {{-- Display general error message --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- Display logout success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email"
                       class="@error('email') is-invalid @enderror"
                       required>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Masukkan kata sandi"
                       class="@error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="login-btn" id="loginButton">
                <span id="loginText">Login</span>
            </button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginButton = document.getElementById('loginButton');
            const loginText = document.getElementById('loginText');

            // Disable button and show loading state
            loginButton.disabled = true;
            loginText.textContent = 'Memproses...';

            // Re-enable button after a delay if form doesn't redirect
            setTimeout(() => {
                loginButton.disabled = false;
                loginText.textContent = 'Login';
            }, 5000);
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
