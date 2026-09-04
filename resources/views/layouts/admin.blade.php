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
        table.sortable thead th {
            background: #fce7f3 !important;
            color: #831843 !important;
            font-weight: 700;
        }
        table.sortable thead th[data-sortable="true"] {
            cursor: pointer;
            user-select: none;
        }
        table.sortable thead th[data-sortable="true"]:hover {
            background: #fbcfe8 !important;
        }
        table.sortable thead th[data-sortable="true"]::after {
            content: " \2195";
            opacity: .4;
            font-size: .8em;
        }
        table.sortable thead th.sort-asc::after { content: " \25B2"; opacity: 1; }
        table.sortable thead th.sort-desc::after { content: " \25BC"; opacity: 1; }
        .admin-filter-btn {
            background: #ff6b9d;
            color: #000;
            font-weight: 700;
            border: 2px solid #000;
        }
        .admin-filter-btn:hover { background: #ff4d8b; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table.sortable').forEach(function (table) {
                var ths = table.querySelectorAll('thead th');
                ths.forEach(function (th, idx) {
                    if (th.hasAttribute('data-nosort')) return;
                    th.setAttribute('data-sortable', 'true');
                    th.addEventListener('click', function () {
                        var tbody = table.querySelector('tbody');
                        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr')).filter(function (r) {
                            return r.children.length === ths.length;
                        });
                        var asc = !th.classList.contains('sort-asc');
                        ths.forEach(function (x) { x.classList.remove('sort-asc', 'sort-desc'); });
                        th.classList.add(asc ? 'sort-asc' : 'sort-desc');
                        rows.sort(function (a, b) {
                            var av = (a.children[idx].getAttribute('data-sort') || a.children[idx].textContent).trim();
                            var bv = (b.children[idx].getAttribute('data-sort') || b.children[idx].textContent).trim();
                            var na = parseFloat(av.replace(/[^\d.\-]/g, ''));
                            var nb = parseFloat(bv.replace(/[^\d.\-]/g, ''));
                            if (!isNaN(na) && !isNaN(nb) && av.match(/\d/) && bv.match(/\d/)) {
                                return asc ? na - nb : nb - na;
                            }
                            return asc ? av.localeCompare(bv, 'id') : bv.localeCompare(av, 'id');
                        });
                        rows.forEach(function (r) { tbody.appendChild(r); });
                    });
                });
            });
        });
    </script>
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
                    MEMBER
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
                <a href="{{ route('admin.teams.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zM7 10a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    TIM
                </a>
                <a href="{{ route('admin.captains.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.captains.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    KAPTEN
                </a>
                <a href="{{ route('admin.maps.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.maps.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    PETA
                </a>

                <div class="px-4 mt-4 mb-2 text-xs font-bold text-black uppercase tracking-wider">
                    Diskografi
                </div>
                <a href="{{ route('admin.songs.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.songs.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                    LAGU
                </a>
                <a href="{{ route('admin.albums.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.albums.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><circle cx="12" cy="12" r="9" stroke-linejoin="round"/><circle cx="12" cy="12" r="3"/></svg>
                    ALBUM / EP
                </a>
                <a href="{{ route('admin.setlists.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.setlists.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
                    SETLIST
                </a>
                <a href="{{ route('admin.coupling-songs.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.coupling-songs.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h6v6H4zM14 14h6v6h-6zM14 4h6v6h-6zM4 14h6v6H4z"/></svg>
                    COUPLING
                </a>
                <a href="{{ route('admin.sub-units.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.sub-units.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M9 7a3 3 0 116 0 3 3 0 01-6 0z"/></svg>
                    SUB-UNIT
                </a>
                <a href="{{ route('admin.mv-locations.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.mv-locations.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    LOKASI MV
                </a>

                <div class="px-4 mt-4 mb-2 text-xs font-bold text-black uppercase tracking-wider">
                    Dokumentasi
                </div>
                <a href="{{ route('admin.docs.api') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.docs.api') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                    API
                </a>
                <a href="{{ route('admin.docs.telegram') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.docs.telegram') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                    TELEGRAM BOT
                </a>
                <a href="{{ route('admin.docs.discord') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.docs.discord') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.87 9.87 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    DISCORD BOT
                </a>

                <div class="px-4 mt-4 mb-2 text-xs font-bold text-black uppercase tracking-wider">
                    Akun
                </div>
                <a href="{{ route('admin.password.edit') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm text-black font-bold hover:bg-yellow-100 {{ request()->routeIs('admin.password.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    UBAH PASSWORD
                </a>
            </nav>

            <div class="p-4 space-y-2" style="border-top:3px solid #000;">
                <a href="{{ route('dashboard') }}" class="text-sm text-black font-bold hover:underline flex items-center gap-2">
                    &larr; KEMBALI KE BERANDA
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
                <h1 class="text-2xl display text-black">@yield('page_title', 'Beranda')</h1>
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
