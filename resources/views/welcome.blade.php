<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-4">feedback</h1>
        <p class="text-gray-600 mb-6">Welcome</p>

        <div class="flex gap-3 justify-center">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="px-4 py-2 rounded bg-black text-white">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded bg-black text-white">
                    Login
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 rounded border border-black">
                        Register
                    </a>
                @endif
            @endauth
        </div>
    </div>
</body>
</html>
