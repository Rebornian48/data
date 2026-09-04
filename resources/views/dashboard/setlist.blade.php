@extends('layouts.app')

@section('title', $setlist->name.' - Setlist JKT48')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-4">
        <a href="{{ route('setlists.index') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
            &larr; Kembali ke daftar setlist
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 mb-6">
        <div class="flex items-center gap-3 mb-3">
            <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase
                {{ $setlist->type === 'special' ? 'bg-amber-500 text-white' : 'bg-brand text-white' }}">
                {{ $setlist->type }}
            </span>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ $setlist->songs->count() }} lagu
            </span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $setlist->name }}</h1>
        @if ($setlist->description)
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $setlist->description }}</p>
        @endif
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900/50 text-left">
                <tr class="text-slate-500 dark:text-slate-400">
                    <th class="px-4 py-2 font-medium w-12">#</th>
                    <th class="px-4 py-2 font-medium">Judul</th>
                    <th class="px-4 py-2 font-medium">Judul Asal</th>
                    <th class="px-4 py-2 font-medium">Asal</th>
                    <th class="px-4 py-2 font-medium">Single</th>
                    <th class="px-4 py-2 font-medium text-right">Preview</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($setlist->songs as $song)
                    <tr>
                        <td class="px-4 py-2 text-slate-400">{{ $song->pivot->position }}</td>
                        <td class="px-4 py-2 font-medium text-slate-800 dark:text-slate-200">
                            {{ $song->title }}
                        </td>
                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400 italic">
                            {{ $song->title_original ?? '—' }}
                        </td>
                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400">
                            {{ $song->origin_group ?? '—' }}
                        </td>
                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400">
                            @if ($song->single)
                                <a href="{{ route('singles.show', $song->single) }}" class="hover:underline">
                                    {{ $song->single->code }}
                                </a>
                            @else
                                —
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
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada lagu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
