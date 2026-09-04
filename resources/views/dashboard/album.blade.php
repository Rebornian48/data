@extends('layouts.app')

@section('title', $album->title.' - Album JKT48')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-4">
        <a href="{{ route('albums.index') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
            &larr; Kembali ke daftar album
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 mb-6">
        <div class="flex items-center gap-3 mb-3 flex-wrap">
            <span class="text-xs font-bold bg-brand text-white px-2.5 py-1 rounded-full uppercase">
                {{ $album->type }}
            </span>
            <span class="text-xs text-slate-400 dark:text-slate-500">#{{ $album->sequence }}</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $album->title }}</h1>
        @if ($album->title_jp)
            <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                JP: <span class="italic">{{ $album->title_jp }}</span>
            </div>
        @endif
        <div class="mt-4 text-sm text-slate-500 dark:text-slate-400 space-y-1">
            <div>
                Tanggal Rilis:
                <span class="font-medium text-slate-700 dark:text-slate-300">
                    {{ $album->release_date?->format('d M Y') ?? 'TBD' }}
                </span>
            </div>
            <div>
                Jumlah Track:
                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $album->tracks->count() }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-baseline justify-between">
            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Tracklist</h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ $album->tracks->whereNotNull('song_id')->count() }} / {{ $album->tracks->count() }} ter-link ke katalog lagu
            </span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900/50 text-left">
                <tr class="text-slate-500 dark:text-slate-400">
                    <th class="px-4 py-2 font-medium w-12">#</th>
                    <th class="px-4 py-2 font-medium">Judul</th>
                    <th class="px-4 py-2 font-medium">Judul Asal</th>
                    <th class="px-4 py-2 font-medium">Asal</th>
                    <th class="px-4 py-2 font-medium text-right">Preview</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach ($album->tracks as $track)
                    <tr>
                        <td class="px-4 py-2 text-slate-400">{{ $track->position }}</td>
                        <td class="px-4 py-2 font-medium text-slate-800 dark:text-slate-200">
                            {{ $track->title }}
                        </td>
                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400 italic">
                            {{ $track->song?->title_original ?? '—' }}
                        </td>
                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400">
                            {{ $track->song?->origin_group ?? '—' }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if ($track->song?->preview_url)
                                <a href="{{ $track->song->preview_url }}" target="_blank" rel="noopener"
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
@endsection
