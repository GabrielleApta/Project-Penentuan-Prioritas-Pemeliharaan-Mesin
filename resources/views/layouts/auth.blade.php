<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #d4eaf7;
        }
        .container-auth {
            display: flex;
            width: 900px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .auth-form {
            flex: 1;
            padding: 40px;
        }
        .auth-form h3 {
            color: #3498db;
            margin-bottom: 20px;
        }
        .auth-form .btn-primary {
            background-color: #3498db;
            border: none;
        }
        .auth-info {
            flex: 1;
            background: #a0d2f0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }
        .auth-info img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .auth-info h2 {
            font-size: 1.5rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-auth">
        <div class="auth-form">
            <h3>@yield('auth-title')</h3>
            @yield('content')
        </div>
        <div class="auth-info">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <h2> Sistem Informasi apaya </h2>
            <p> PT. Arteria Daya Mulia </p>
        </div>
    </div>
</body>
</html>
