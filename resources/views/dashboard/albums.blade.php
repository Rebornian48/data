@extends('layouts.app')

@section('title', 'Album & EP - JKT48 Database')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Album & EP</h1>
        <p class="text-slate-500 dark:text-slate-400">
            {{ $albums->where('type', 'album')->count() }} album studio &middot;
            {{ $albums->where('type', 'ep')->count() }} mini album / EP.
        </p>
    </div>

    @foreach (['album' => 'Album Studio', 'ep' => 'Mini Album / EP'] as $type => $label)
        @php $group = $albums->where('type', $type)->values(); @endphp
        @if ($group->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-3">{{ $label }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($group as $album)
                        <a href="{{ route('albums.show', $album) }}"
                           class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold bg-brand text-white px-2.5 py-1 rounded-full uppercase">
                                    {{ $album->type }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $album->release_date?->format('d M Y') ?? 'TBD' }}
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg leading-tight mb-1">
                                {{ $album->title }}
                            </h3>
                            @if ($album->title_jp)
                                <div class="text-xs italic text-slate-500 dark:text-slate-400 mb-3">
                                    {{ $album->title_jp }}
                                </div>
                            @endif
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700">
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $album->tracks_count }} track
                                </div>
                                <div class="text-xs text-slate-400 dark:text-slate-500">
                                    #{{ $album->sequence }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if ($albums->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
            <div class="text-slate-400 text-lg">Belum ada album yang tercatat.</div>
        </div>
    @endif
</div>
@endsection
