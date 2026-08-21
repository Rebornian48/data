<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'JKT48 Database')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg, #E60012 0%, #ff4d6d 100%); }
        .text-brand { color: #E60012; }
        .bg-brand { background-color: #E60012; }
        .sidebar-link.active {
            background-color: #fef2f2;
            color: #E60012;
            border-right: 3px solid #E60012;
        }
    </style>
</head>
<body class="bg-slate-100">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
            <div class="p-5 border-b border-slate-200">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 gradient-brand rounded-lg flex items-center justify-center text-white font-black">
                        48
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">JKT48 Admin</div>
                        <div class="text-xs text-slate-500">Database Panel</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 py-4">
                <div class="px-4 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Management
                </div>
                <a href="{{ route('admin.members.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Members
                </a>
                <a href="{{ route('admin.generations.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('admin.generations.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Generasi
                </a>
                <a href="{{ route('admin.singles.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('admin.singles.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                    Single
                </a>
                <a href="{{ route('admin.captains.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('admin.captains.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Kapten
                </a>
            </nav>

            <div class="p-4 border-t border-slate-200">
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 overflow-x-auto">
            <header class="bg-white border-b border-slate-200 px-8 py-4">
                <h1 class="text-2xl font-bold text-slate-900">@yield('page_title', 'Dashboard')</h1>
                @hasSection('page_subtitle')
                    <p class="text-sm text-slate-500 mt-1">@yield('page_subtitle')</p>
                @endif
            </header>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
