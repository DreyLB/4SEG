<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laravel</title>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <style>
        .div {
            width: 100%;
            height: 900px;
            display: grid;
            align-items: center;
            justify-items: center;
            gap: 20px;
        }
        .green {
            color: green;
            font-size: 50px;
        }
        .btn {
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background-color: #28a745; /* verde */
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
        }
        .btn-register {
            background-color: #007bff; /* azul */
        }
        .btn-register:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="div">
        <h1 class="green">AUTENTICADO</h1>

        <!-- Botão Login -->
        <a href="{{ route('login') }}" class="btn">Login</a>

        <!-- Botão Register -->
        <a href="{{ route('register') }}" class="btn btn-register">Register</a>
    </div>
</body>
</html>
