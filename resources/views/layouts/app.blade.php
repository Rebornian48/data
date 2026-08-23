<!DOCTYPE html>
<html lang="id" x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': dark }" x-init="$watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', dark)">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JKT48 Database')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg, #E60012 0%, #ff4d6d 100%); }
        .text-brand { color: #E60012; }
        .bg-brand { background-color: #E60012; }
        .border-brand { border-color: #E60012; }
        .hover\:bg-brand-dark:hover { background-color: #b8000e; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen">
    <nav class="bg-white dark:bg-slate-800 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 gradient-brand rounded-lg flex items-center justify-center text-white font-black text-lg">
                            48
                        </div>
                        <span class="font-bold text-xl text-slate-900 dark:text-slate-100">JKT48 DB</span>
                    </a>
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-brand' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('members.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('members.*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Members
                        </a>
                        <a href="{{ route('singles.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('singles.*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Singles
                        </a>
                        <a href="{{ route('captains.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('captains.*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Captains
                        </a>
                        <a href="{{ route('sorter.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('sorter.*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Sorter
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="dark = !dark" class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                        <template x-if="dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </template>
                        <template x-if="!dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </template>
                    </button>
                    <a href="{{ route('admin.home') }}"
                       class="text-sm px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-lg hover:bg-slate-800 dark:hover:bg-slate-600">
                        Admin Panel
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
            JKT48 Database &copy; {{ date('Y') }}
        </div>
    </footer>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>