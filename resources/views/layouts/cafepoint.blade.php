<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CafePoint</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        @include ('components.sidebar')
        <div class="flex flex-col flex-1 overflow-hidden">
        {{-- Navbar --}}
         @include ('components.navbar')
         <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
         </main>
        </div>
    </div>
</body>
</html>