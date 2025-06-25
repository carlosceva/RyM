<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Iniciar sesión | RyM</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #FFA500;
            --secondary: #FFCC00;
            --bg: #FDFDFC;
            --text: #1b1b18;
        }
    </style>
</head>
<body class="bg-[--bg] text-[--text] flex items-center justify-center min-h-screen px-4">

    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">

        <!-- Logo -->
        <div class="bg-white p-3 rounded shadow w-32 mx-auto mb-6">
            <img src="{{ asset('img/logonuevo.jpeg') }}" alt="Logo RyM" class="w-full h-auto object-contain">
        </div>

        <!-- Título -->
        <h1 class="text-2xl font-semibold text-center text-[--primary] mb-6">Iniciar sesión</h1>

        <!-- Formulario -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Código -->
            <div class="mb-4">
                <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                <input id="codigo" name="codigo" type="text" required autofocus
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[--primary]"
                    value="{{ old('codigo') }}"
                    oninput="this.value = this.value.toLowerCase();">

                @error('codigo')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input id="password" name="password" type="password" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[--primary]" autocomplete="current-password">
                @error('password')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Recordarme -->
            <div class="flex items-center mb-4">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[--primary] shadow-sm focus:ring-[--primary]" name="remember">
                <label for="remember_me" class="ms-2 text-sm text-gray-600">Recordarme</label>
            </div>

            <!-- Enlaces y botón -->
            <div class="flex items-center justify-between">
               
                <button type="submit" class="px-5 py-2 bg-[--primary] text-white rounded hover:bg-orange-600 transition">
                    Ingresar
                </button>
            </div>
        </form>
    </div>

</body>
</html>
