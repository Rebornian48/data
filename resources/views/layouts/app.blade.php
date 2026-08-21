<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JKT48 Database')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 gradient-brand rounded-lg flex items-center justify-center text-white font-black text-lg">
                            48
                        </div>
                        <span class="font-bold text-xl text-slate-900">JKT48 DB</span>
                    </a>
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-brand' : 'text-slate-600 hover:text-slate-900' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('members.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('members.*') ? 'text-brand' : 'text-slate-600 hover:text-slate-900' }}">
                            Members
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.home') }}"
                       class="text-sm px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800">
                        Admin Panel
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-slate-500">
            JKT48 Database &copy; {{ date('Y') }}
        </div>
    </footer>
</body>
</html>
