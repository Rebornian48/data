@extends('layouts.app')

@section('title', 'Statistik - JKT48 Database')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Statistik</h1>
        <p class="text-slate-500 dark:text-slate-400">Daftar Jumlah Member JKT48 per Generasi</p>
    </div>

    {{-- Section: current --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 mb-6"
         style="border:3px solid #000;box-shadow:6px 6px 0 #000;">
        <h2 class="text-lg font-bold text-slate-900 mb-2">
            Total member JKT48 saat ini : <span class="text-red-600">{{ $totals['active'] }} member</span>
        </h2>
        <p class="text-sm text-slate-500 mb-4">(termasuk member JKT48V)</p>

        <ul class="space-y-1 text-slate-800">
            @foreach ($rows as $code => $r)
                @if ($r['active'] > 0)
                    <li class="flex justify-between border-b border-dashed border-slate-200 py-1">
                        <span class="font-medium">{{ $r['label'] }}</span>
                        <span class="font-bold">{{ $r['active'] }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    {{-- Section: all-time --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 mb-6"
         style="border:3px solid #000;box-shadow:6px 6px 0 #000;">
        <h2 class="text-lg font-bold text-slate-900 mb-4">
            Total member JKT48 dari awal hingga sekarang : <span class="text-red-600">{{ $totals['all'] }} member</span>
        </h2>

        <ul class="space-y-1 text-slate-800">
            @foreach ($rows as $code => $r)
                @if ($r['total'] > 0)
                    <li class="flex justify-between border-b border-dashed border-slate-200 py-1">
                        <span class="font-medium">{{ $r['label'] }}</span>
                        <span><span class="font-bold">{{ $r['active'] }}</span> dari {{ $r['total'] }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    {{-- Section: new era survivors --}}
    @php
        $survTotal = $totals['survivors'] - ($rows['10']['survivors'] ?? 0);
        $survActiveTotal = $totals['survivorsActive'] - ($rows['10']['survivorsActive'] ?? 0);
    @endphp
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 mb-6"
         style="border:3px solid #000;box-shadow:6px 6px 0 #000;">
        <h2 class="text-lg font-bold text-slate-900 mb-4">
            Total member JKT48 yang bertahan semenjak JKT48 New Era (Maret 2021):
            <span class="text-red-600">{{ $survActiveTotal }} dari {{ $survTotal }} member</span>
        </h2>

        <ul class="space-y-1 text-slate-800">
            @foreach ($rows as $code => $r)
                @continue($code === '10')
                @if ($r['survivors'] > 0)
                    <li class="flex justify-between border-b border-dashed border-slate-200 py-1">
                        <span class="font-medium">{{ $r['label'] }}</span>
                        <span><span class="font-bold">{{ $r['survivorsActive'] }}</span> dari {{ $r['survivors'] }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    {{-- Section: formation dates --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700"
         style="border:3px solid #000;box-shadow:6px 6px 0 #000;">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Tanggal Terbentuknya Generasi di JKT48</h2>
        <ul class="space-y-1 text-slate-800">
            @foreach ($formationDates as $code => $date)
                <li class="border-b border-dashed border-slate-200 py-1">
                    <span class="font-medium">{{ $code === 'V' ? 'JKT48V' : 'Generasi '.$code }}:</span>
                    <span>{{ $date }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
