<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'JKT48 Database')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/neo.css') }}?v=1">
    <style>
        .sidebar-link.active {
            background: #ffd23f !important;
            color: #000 !important;
            border-right: 6px solid #000 !important;
        }
    </style>
</head>
<body class="neo-body">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 flex flex-col" style="background:#fff;border-right:3px solid #000;">
            <div class="p-5 flex items-center gap-3" style="border-bottom:3px solid #000;">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 flex items-center justify-center text-black font-black" style="background:#ff6b9d;border:3px solid #000;box-shadow:3px 3px 0 #000;">
                        48
                    </div>
                    <div>
                        <div class="display text-black">JKT48 ADMIN</div>
                        <div class="text-xs font-bold text-black">Database Panel</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 py-4">
                <div class="px-4 mb-2 text-xs font-bold text-black uppercase tracking-wider">
                    Management
                </div>
                <a href="{{ route('admin.members.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    MEMBERS
                </a>
                <a href="{{ route('admin.generations.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.generations.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    GENERASI
                </a>
                <a href="{{ route('admin.singles.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.singles.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    SINGLE
                </a>
                <a href="{{ route('admin.captains.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.captains.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    KAPTEN
                </a>
            </nav>

            <div class="p-4 space-y-2" style="border-top:3px solid #000;">
                <a href="{{ route('dashboard') }}" class="text-sm text-black font-bold hover:underline flex items-center gap-2">
                    &larr; KEMBALI KE DASHBOARD
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-bold flex items-center gap-2 w-full px-3 py-2" style="background:#ef4444;border:3px solid #000;box-shadow:3px 3px 0 #000;color:#000;">
                        LOGOUT
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 overflow-x-auto">
            <header class="px-8 py-4" style="background:#fff;border-bottom:3px solid #000;">
                <h1 class="text-2xl display text-black">@yield('page_title', 'Dashboard')</h1>
                @hasSection('page_subtitle')
                    <p class="text-sm font-bold text-black mt-1">@yield('page_subtitle')</p>
                @endif
            </header>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 px-4 py-3 flex items-center gap-2" style="background:#a3e635;border:3px solid #000;box-shadow:4px 4px 0 #000;color:#000;font-weight:700;">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 px-4 py-3" style="background:#ef4444;border:3px solid #000;box-shadow:4px 4px 0 #000;color:#000;font-weight:700;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
