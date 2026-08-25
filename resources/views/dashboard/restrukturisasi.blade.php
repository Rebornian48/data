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
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 mb-1">Restrukturisasi</h1>
        <p class="text-slate-500">Member yang lulus pada 11 Maret 2021 &ndash; 14 Maret 2021.</p>
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
