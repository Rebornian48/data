@extends('layouts.app')

@section('title', $single->title.' - Single JKT48')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-4">
        <a href="{{ route('singles.index') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
            &larr; Kembali ke daftar single
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-start gap-6">
            @if ($single->cover_art_url)
                <img src="{{ $single->cover_art_url }}" alt="Cover {{ $single->title }}"
                     class="w-40 h-40 rounded-lg object-cover flex-shrink-0" />
            @endif

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-3 flex-wrap">
                    <span class="text-xs font-bold bg-brand text-white px-2.5 py-1 rounded-full">
                        {{ $single->code }}
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">#{{ $single->sequence }}</span>
                    @if ($single->origin_group)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                            Original: {{ $single->origin_group }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $single->title }}</h1>
                @if ($single->title_jp && $single->title_jp !== $single->title)
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        JP: <span class="italic">{{ $single->title_jp }}</span>
                    </div>
                @endif

                <div class="mt-4 text-sm text-slate-500 dark:text-slate-400 space-y-1">
                    <div>
                        Tanggal Rilis:
                        <span class="font-medium text-slate-700 dark:text-slate-300">
                            {{ $single->release_date?->format('d M Y') ?? ($single->notes ?? 'TBD') }}
                        </span>
                    </div>
                    <div>
                        Total senbatsu:
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $centers->count() + $senbatsu->count() }} member</span>
                        @if ($centers->count())
                            &middot; Center: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $centers->count() }} member</span>
                        @endif
                    </div>
                    @if ($single->mv_title)
                        <div>
                            MV YouTube:
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $single->mv_title }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($centers->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-baseline justify-between mb-3">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Center</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">Diurutkan berdasarkan nama lengkap (A–Z)</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($centers as $member)
                    @include('dashboard.partials.single-member-card', ['member' => $member, 'isCenter' => true])
                @endforeach
            </div>
        </div>
    @endif

    @if ($senbatsu->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-baseline justify-between mb-3">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Senbatsu</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">Diurutkan berdasarkan nama lengkap (A–Z)</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($senbatsu as $member)
                    @include('dashboard.partials.single-member-card', ['member' => $member, 'isCenter' => false])
                @endforeach
            </div>
        </div>
    @endif

    @if ($single->songs->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-baseline justify-between mb-3">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Daftar Lagu</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $single->songs->count() }} lagu dari katalog Diskografi</span>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-left">
                        <tr class="text-slate-500 dark:text-slate-400">
                            <th class="px-4 py-2 font-medium">Judul</th>
                            <th class="px-4 py-2 font-medium">Judul Asal</th>
                            <th class="px-4 py-2 font-medium">Asal</th>
                            <th class="px-4 py-2 font-medium">Status</th>
                            <th class="px-4 py-2 font-medium text-right">Preview</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($single->songs as $song)
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-800 dark:text-slate-200">
                                    {{ $song->title }}
                                </td>
                                <td class="px-4 py-2 text-slate-500 dark:text-slate-400 italic">
                                    {{ $song->title_original ?? '—' }}
                                </td>
                                <td class="px-4 py-2 text-slate-500 dark:text-slate-400">
                                    {{ $song->origin_group ?? '—' }}
                                </td>
                                <td class="px-4 py-2">
                                    @if ($song->released)
                                        <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">Rilis</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">Belum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    @if ($song->preview_url)
                                        <a href="{{ $song->preview_url }}" target="_blank" rel="noopener"
                                           class="text-xs text-brand hover:underline">YouTube ↗</a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($single->couplingSongs->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-baseline justify-between mb-3">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Coupling Songs</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $single->couplingSongs->count() }} lagu B-side</span>
            </div>
            <div class="space-y-8">
                @foreach ($single->couplingSongs as $coupling)
                    @php
                        $couplingCenters = $coupling->members->filter(fn ($m) => $m->pivot->role === 'center')->sortBy('name')->values();
                        $couplingMembers = $coupling->members->filter(fn ($m) => $m->pivot->role !== 'center')->sortBy('name')->values();
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                        <div class="mb-4">
                            <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg">{{ $coupling->title }}</h3>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                @if ($coupling->title_jp)
                                    <span class="italic">{{ $coupling->title_jp }}</span> &middot;
                                @endif
                                {{ $coupling->origin_group }}
                                @if ($coupling->release_year)
                                    &middot; {{ $coupling->release_year }}
                                @endif
                                &middot; {{ $coupling->members->count() }} member
                            </div>
                        </div>

                        @if ($couplingCenters->isNotEmpty())
                            <div class="mb-4">
                                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Center</div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    @foreach ($couplingCenters as $member)
                                        @include('dashboard.partials.single-member-card', ['member' => $member, 'isCenter' => true])
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($couplingMembers->isNotEmpty())
                            <div>
                                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Member</div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    @foreach ($couplingMembers as $member)
                                        @include('dashboard.partials.single-member-card', ['member' => $member, 'isCenter' => false])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($centers->isEmpty() && $senbatsu->isEmpty() && $single->songs->isEmpty() && $single->couplingSongs->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
            <div class="text-slate-400 text-lg">Belum ada data untuk single ini.</div>
        </div>
    @endif
</div>
@endsection
