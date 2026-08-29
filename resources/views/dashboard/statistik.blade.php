@extends('layouts.app')

@section('title', 'Statistik - JKT48 Database')

@section('content')
@php
    $survTotal = $totals['survivors'] - ($rows['10']['survivors'] ?? 0);
    $survActiveTotal = $totals['survivorsActive'] - ($rows['10']['survivorsActive'] ?? 0);
@endphp
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Statistik</h1>
        <p class="text-slate-500 dark:text-slate-400">Daftar Jumlah Member JKT48 per Generasi</p>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4" style="border:3px solid #000;box-shadow:4px 4px 0 #000;">
            <p class="text-xs font-bold uppercase text-slate-500">Aktif Saat Ini</p>
            <p class="text-2xl font-black text-red-600">{{ $totals['active'] }} <span class="text-sm text-slate-700">member</span></p>
            <p class="text-xs text-slate-500">termasuk JKT48V</p>
        </div>
        <div class="bg-white p-4" style="border:3px solid #000;box-shadow:4px 4px 0 #000;">
            <p class="text-xs font-bold uppercase text-slate-500">Total Sepanjang Masa</p>
            <p class="text-2xl font-black text-red-600">{{ $totals['all'] }} <span class="text-sm text-slate-700">member</span></p>
        </div>
        <div class="bg-white p-4" style="border:3px solid #000;box-shadow:4px 4px 0 #000;">
            <p class="text-xs font-bold uppercase text-slate-500">Bertahan Sejak New Era (Mar 2021)</p>
            <p class="text-2xl font-black text-red-600">{{ $survActiveTotal }} <span class="text-sm text-slate-700">dari {{ $survTotal }}</span></p>
        </div>
    </div>

    {{-- Combined table --}}
    <div class="bg-white mb-6 overflow-x-auto" style="border:3px solid #000;box-shadow:6px 6px 0 #000;">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-900 text-white">
                    <th class="text-left py-2 px-3 font-bold">Generasi</th>
                    <th class="text-right py-2 px-3 font-bold">Aktif</th>
                    <th class="text-right py-2 px-3 font-bold">Total</th>
                    <th class="text-right py-2 px-3 font-bold">Aktif / Total</th>
                    <th class="text-right py-2 px-3 font-bold">Sejak New Era</th>
                    <th class="text-left py-2 px-3 font-bold">Tanggal Terbentuk</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $code => $r)
                    @if ($r['total'] > 0 || ($formationDates[$code] ?? false))
                        <tr class="border-t border-slate-200 hover:bg-slate-50">
                            <td class="py-2 px-3 font-bold">{{ $r['label'] }}</td>
                            <td class="py-2 px-3 text-right {{ $r['active'] > 0 ? 'text-red-600 font-bold' : 'text-slate-400' }}">
                                {{ $r['active'] }}
                            </td>
                            <td class="py-2 px-3 text-right text-slate-700">{{ $r['total'] }}</td>
                            <td class="py-2 px-3 text-right text-slate-700">
                                <span class="font-bold">{{ $r['active'] }}</span> / {{ $r['total'] }}
                            </td>
                            <td class="py-2 px-3 text-right text-slate-700">
                                @if ($code === '10' || $r['survivors'] === 0)
                                    <span class="text-slate-300"></span>
                                @else
                                    <span class="font-bold">{{ $r['survivorsActive'] }}</span> / {{ $r['survivors'] }}
                                @endif
                            </td>
                            <td class="py-2 px-3 text-slate-600 text-xs">
                                {{ $formationDates[$code] ?? '' }}
                                @if (empty($formationDates[$code]))
                                    <span class="text-slate-400 italic">belum diisi</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-black bg-slate-100">
                    <td class="py-2 px-3 font-black">TOTAL</td>
                    <td class="py-2 px-3 text-right font-black text-red-600">{{ $totals['active'] }}</td>
                    <td class="py-2 px-3 text-right font-black">{{ $totals['all'] }}</td>
                    <td class="py-2 px-3 text-right font-black">{{ $totals['active'] }} / {{ $totals['all'] }}</td>
                    <td class="py-2 px-3 text-right font-black">{{ $survActiveTotal }} / {{ $survTotal }}</td>
                    <td class="py-2 px-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="text-xs text-slate-500">
        Catatan: Generasi 10 dikecualikan dari kolom "Sejak New Era" karena dibubarkan Des 2020 lalu dibentuk kembali Des 2021.
    </p>
</div>
@endsection
