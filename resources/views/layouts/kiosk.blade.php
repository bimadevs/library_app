<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Kiosk</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|jetbrains-mono:400,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50 overflow-hidden">
        <div class="min-h-screen flex flex-col relative">
            <!-- Background Elements -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-emerald-400/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-blue-400/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Header -->
            <header class="relative z-10 w-full px-8 py-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <x-application-logo class="w-12 h-12 text-emerald-600" />
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 tracking-tight leading-none">Perpustakaan</h1>
                        <p class="text-sm text-slate-500 font-medium">Sistem Buku Tamu Digital</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-slate-800 font-mono" id="clock">00:00</p>
                    <p class="text-slate-500 text-sm font-medium">{{ now()->format('l, d F Y') }}</p>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 relative z-10 flex flex-col p-6 overflow-hidden">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="relative z-10 py-4 text-center text-slate-400 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Tap anywhere to start or scan your card.
            </footer>
        </div>

        <script>
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                document.getElementById('clock').textContent = `${hours}:${minutes}`;
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>
    </body>
</html>
