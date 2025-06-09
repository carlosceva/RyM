<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>RyM | Plataforma de Gestión</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #FFA500;
            --secondary: #FFCC00;
            --text: #1b1b18;
            --bg: #FDFDFC;
        }
    </style>
</head>
<body class="bg-[--bg] text-[--text] flex items-center justify-center min-h-screen px-4 lg:px-8">

    <div class="w-full max-w-5xl bg-white rounded-lg shadow-lg overflow-hidden flex flex-col lg:flex-row">
        
        <!-- Contenido -->
        <div class="w-full lg:w-1/2 p-8 flex flex-col justify-center">
            <div class="bg-white p-3 rounded shadow w-36 mb-6">
                <img src="{{ asset('img/logonuevo.jpeg') }}" alt="Logo RyM" class="w-full h-auto object-contain">
            </div>

            <h1 class="text-3xl font-semibold mb-3 text-[--primary]">Bienvenido a RyM</h1>
            <p class="mb-4 text-sm text-gray-700">
                Plataforma desarrollada para mejorar la gestión de solicitudes de la empresa <strong>RyM</strong>. Este sistema busca optimizar procesos de forma ágil y efectiva.
            </p>

            @if (Route::has('login'))
                <div class="mt-6 flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-[--primary] text-white rounded hover:bg-orange-600 transition">
                            Ir al Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2 border border-[--primary] text-[--primary] rounded hover:bg-[--primary] hover:text-white transition">
                            Iniciar sesión
                        </a>
                    @endauth
                </div>
            @endif
        </div>

        <!-- Imagen -->
        <div class="w-full lg:w-1/2 h-64 lg:h-auto bg-cover bg-center" style="background-image: url('{{ asset('img/portada.jpg') }}')">
            <!-- Puedes quitar el overlay oscuro, ya no es necesario -->
        </div>
    </div>

</body>
</html>
