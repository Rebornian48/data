@extends('layouts.app')

@section('title', 'Restrukturisasi - JKT48 Database')

@section('content')
<style>
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
    .member-thumb {
        width: 40px; height: 40px; border-radius: 9999px; object-fit: cover;
        background: #f1f5f9; border: 1px solid #e2e8f0;
    }
    .member-thumb-fallback {
        width: 40px; height: 40px; border-radius: 9999px;
        background: #fce7f3; color: #831843;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .75rem;
        border: 1px solid #e2e8f0;
    }
    .restruk-header {
        display: flex; align-items: center; gap: 1.25rem; margin-bottom: 2rem;
    }
    .restruk-sakura {
        width: 88px; height: 88px; flex-shrink: 0;
        object-fit: contain;
    }
    .restruk-title {
        font-size: 3rem; line-height: 1.05; font-weight: 900;
        color: #831843; letter-spacing: -0.02em; margin: 0;
    }
    .restruk-subtitle {
        font-size: 1.5rem; font-weight: 700; color: #475569; margin-top: .25rem;
    }
    @media (max-width: 640px) {
        .restruk-sakura { width: 56px; height: 56px; }
        .restruk-title { font-size: 2rem; }
        .restruk-subtitle { font-size: 1.05rem; }
    }
    .carousel {
        position: relative; overflow: hidden; border: 3px solid #000;
        border-radius: 1rem; background: #000; box-shadow: 6px 6px 0 #000;
        margin-bottom: 2.5rem;
    }
    .carousel-track {
        display: flex; transition: transform .5s ease;
    }
    .carousel-slide {
        flex: 0 0 100%; position: relative;
    }
    .carousel-slide img {
        display: block; width: 100%; height: auto; max-height: 70vh; object-fit: cover;
    }
    .carousel-caption {
        position: absolute; left: 0; right: 0; bottom: 0;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(to top, rgba(0,0,0,.85), rgba(0,0,0,0));
        color: #fff;
        font-family: 'Archivo Black', 'Space Grotesk', sans-serif;
        font-size: 2.5rem; letter-spacing: .04em; text-transform: uppercase;
    }
    @media (max-width: 640px) {
        .carousel-caption { font-size: 1.35rem; padding: .75rem 1rem; }
    }
    .carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 48px; height: 48px; border-radius: 9999px;
        background: rgba(255,255,255,.9); color: #000; font-weight: 900;
        border: 3px solid #000; cursor: pointer; z-index: 3;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .carousel-btn:hover { background: #ff6b9d; color: #fff; }
    .carousel-btn.prev { left: 1rem; }
    .carousel-btn.next { right: 1rem; }
    .carousel-dots {
        position: absolute; bottom: .75rem; left: 50%; transform: translateX(-50%);
        display: flex; gap: .5rem; z-index: 2;
    }
    .carousel-dot {
        width: 12px; height: 12px; border-radius: 9999px;
        background: rgba(255,255,255,.5); border: 2px solid #000; cursor: pointer;
    }
    .carousel-dot.active { background: #ff6b9d; }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="restruk-header">
        <img src="{{ asset('img/restrukturisasi/sakura.png') }}" alt="Sakura" class="restruk-sakura"
             onerror="this.style.display='none'">
        <div>
            <h1 class="restruk-title">RESTRUKTURISASI</h1>
            <p class="restruk-subtitle">Upacara Kelulusan Khusus JKT48 &middot; 11&ndash;14 Maret 2021</p>
        </div>
    </div>

    {{-- Carousel --}}
    @php
        $slides = [
            ['file' => 'restrukturisasi-1.jpg', 'caption' => 'Semua Member'],
            ['file' => 'restrukturisasi-2.jpg', 'caption' => 'Team J'],
            ['file' => 'restrukturisasi-3.jpg', 'caption' => 'Team KIII'],
            ['file' => 'restrukturisasi-4.jpg', 'caption' => 'Team T'],
            ['file' => 'restrukturisasi-5.jpg', 'caption' => 'Academy Class A'],
        ];
    @endphp
    <div class="carousel" id="restrukCarousel" data-count="{{ count($slides) }}">
        <div class="carousel-track">
            @foreach ($slides as $s)
                <div class="carousel-slide">
                    <img src="{{ asset('img/restrukturisasi/' . $s['file']) }}" alt="{{ $s['caption'] }}">
                    <div class="carousel-caption">{{ $s['caption'] }}</div>
                </div>
            @endforeach
        </div>
        <button type="button" class="carousel-btn prev" aria-label="Sebelumnya">&#10094;</button>
        <button type="button" class="carousel-btn next" aria-label="Berikutnya">&#10095;</button>
        <div class="carousel-dots">
            @foreach ($slides as $i => $s)
                <span class="carousel-dot {{ $i === 0 ? 'active' : '' }}" data-idx="{{ $i }}"></span>
            @endforeach
        </div>
    </div>

    {{-- Stats per generation --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        @foreach ($generations as $gen)
            <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition">
                <div class="text-xs font-bold uppercase tracking-wider mb-1"
                     style="color:#831843;">
                    {{ $gen->code }}
                </div>
                <div class="text-sm text-slate-500 truncate" title="{{ $gen->name }}">{{ $gen->name }}</div>
                <div class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($gen->members_count) }}</div>
                <div class="text-xs text-slate-500">member</div>
            </div>
        @endforeach
    </div>

    {{-- Members table --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
            <div class="text-sm text-slate-500">
                Total {{ number_format($members->count()) }} member. Klik header kolom untuk mengurutkan.
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm sortable">
                <thead class="text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left" data-nosort>Foto</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Tanggal Lahir</th>
                        <th class="px-4 py-3 text-center">Usia</th>
                        <th class="px-4 py-3 text-left">Generasi</th>
                        <th class="px-4 py-3 text-left">Tanggal Lulus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($members as $m)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2">
                                @if ($m->photo_url)
                                    <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" class="member-thumb">
                                @else
                                    <span class="member-thumb-fallback">
                                        {{ strtoupper(mb_substr($m->name, 0, 1)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 font-medium text-slate-900">
                                <a href="{{ route('members.show', $m) }}" class="hover:underline">{{ $m->name }}</a>
                            </td>
                            <td class="px-4 py-2 text-slate-600" data-sort="{{ $m->birth_date?->format('Y-m-d') ?? '' }}">
                                {{ $m->birth_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-2 text-center text-slate-700" data-sort="{{ $m->current_age ?? -1 }}">
                                {{ $m->current_age !== null ? number_format($m->current_age, 2) : '-' }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-xs px-2 py-0.5 rounded font-medium"
                                      style="background:#fce7f3;color:#831843;">
                                    {{ $m->generation->code }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-slate-600" data-sort="{{ $m->graduation_date?->format('Y-m-d') ?? '' }}">
                                {{ $m->graduation_date?->format('d M Y') ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        (function () {
            var car = document.getElementById('restrukCarousel');
            if (!car) return;
            var track = car.querySelector('.carousel-track');
            var slides = car.querySelectorAll('.carousel-slide');
            var dots = car.querySelectorAll('.carousel-dot');
            var count = slides.length;
            var idx = 0;
            var timer = null;

            function go(i) {
                idx = (i + count) % count;
                track.style.transform = 'translateX(-' + (idx * 100) + '%)';
                dots.forEach(function (d, di) { d.classList.toggle('active', di === idx); });
            }
            function next() { go(idx + 1); }
            function prev() { go(idx - 1); }
            function restart() {
                if (timer) clearInterval(timer);
                timer = setInterval(next, 5000);
            }

            car.querySelector('.carousel-btn.next').addEventListener('click', function () { next(); restart(); });
            car.querySelector('.carousel-btn.prev').addEventListener('click', function () { prev(); restart(); });
            dots.forEach(function (d) {
                d.addEventListener('click', function () { go(parseInt(d.dataset.idx, 10)); restart(); });
            });
            restart();
        })();

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
@endsection
