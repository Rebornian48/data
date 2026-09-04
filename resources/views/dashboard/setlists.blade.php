@extends('layouts.app')

@section('title', 'Setlist - JKT48 Database')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Setlist</h1>
        <p class="text-slate-500 dark:text-slate-400">
            {{ ($setlists['regular'] ?? collect())->count() }} setlist reguler &middot;
            {{ ($setlists['special'] ?? collect())->count() }} setlist special.
        </p>
    </div>

    @foreach (['regular' => 'Setlist Reguler', 'special' => 'Setlist Special'] as $type => $label)
        @php $group = $setlists[$type] ?? collect(); @endphp
        @if ($group->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-3">{{ $label }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($group as $setlist)
                        <a href="{{ route('setlists.show', $setlist) }}"
                           class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase
                                    {{ $type === 'special'
                                        ? 'bg-amber-500 text-white'
                                        : 'bg-brand text-white' }}">
                                    {{ $type }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $setlist->songs_count }} lagu
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg leading-snug">
                                {{ $setlist->name }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if ($setlists->flatten()->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
            <div class="text-slate-400 text-lg">Belum ada setlist yang tercatat.</div>
        </div>
    @endif
</div>
@endsection
