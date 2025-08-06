<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('{{ asset('images/bg-body.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.3); /* overlay gelap lembut */
            z-index: 0;
        }

        .container-auth {
            display: flex;
            width: 900px;
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1); /* transparan */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            z-index: 1;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-form {
            flex: 1;
            padding: 40px;
            color: #fff;
        }

        .auth-form h3 {
            color: #ffffff;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .auth-form .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .auth-form .form-control::placeholder {
            color: #eee;
        }

        .auth-form .btn-primary {
            background-color: #3498db;
            border: none;
        }

        .auth-info {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .auth-info img {
            max-width: 300px;
            margin-bottom: 20px;
        }

        .auth-info h2, .auth-info p {
            color: white;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.4);
        }

        a.text-primary {
            color: #90c7f5 !important;
        }
    </style>
</head>
<body>
    <div class="container-auth">
        <div class="auth-form">
            <h3>@yield('auth-title', 'Login')</h3>
            @yield('content')
        </div>
        <div class="auth-info">
            <img src="{{ asset('images/logo_arida.png') }}" alt="Logo" onerror="this.style.display='none'">
            <h2>Sistem Penentuan Prioritas Pemeliharaan Mesin Produksi Benang</h2>
            <p>PT. Arteria Daya Mulia</p>
        </div>
    </div>
</body>
</html>
