<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JKT48 Database')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/neo.css') }}?v=1">
</head>
<body class="neo-body min-h-screen">
    <nav class="bg-white sticky top-0 z-50" style="border-bottom: 3px solid #000;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 flex items-center justify-center text-black font-black text-lg" style="background:#ff6b9d;border:3px solid #000;box-shadow:3px 3px 0 #000;">
                            48
                        </div>
                        <span class="display text-xl">JKT48 DB</span>
                    </a>
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-bold {{ request()->routeIs('dashboard') ? 'underline' : 'hover:underline' }}">
                            DASHBOARD
                        </a>
                        <a href="{{ route('members.index') }}"
                           class="text-sm font-bold {{ request()->routeIs('members.*') ? 'underline' : 'hover:underline' }}">
                            MEMBERS
                        </a>
                        <a href="{{ route('singles.index') }}"
                           class="text-sm font-bold {{ request()->routeIs('singles.*') ? 'underline' : 'hover:underline' }}">
                            SINGLES
                        </a>
                        <a href="{{ route('captains.index') }}"
                           class="text-sm font-bold {{ request()->routeIs('captains.*') ? 'underline' : 'hover:underline' }}">
                            CAPTAINS
                        </a>
                        <a href="{{ route('sorter.index') }}"
                           class="text-sm font-bold {{ request()->routeIs('sorter.*') ? 'underline' : 'hover:underline' }}">
                            SORTER
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.home') }}"
                       class="text-sm px-4 py-2" style="background:#ffd23f;border:3px solid #000;box-shadow:3px 3px 0 #000;font-weight:700;">
                        ADMIN
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="mt-16" style="border-top: 3px solid #000; background:#fff;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm font-bold">
            JKT48 DATABASE &copy; {{ date('Y') }}
        </div>
    </footer>
</body>
</html>
