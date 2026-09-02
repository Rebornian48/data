@extends('layouts.app')

@section('title', 'Single - JKT48 Database')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Single</h1>
        <p class="text-slate-500 dark:text-slate-400">Daftar semua single yang dirilis oleh JKT48.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($singles as $single)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold bg-brand text-white px-2.5 py-1 rounded-full">
                        {{ $single->code }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $single->release_date?->format('d M Y') ?? ($single->notes ?? 'TBD') }}
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg leading-tight mb-3">
                    {{ $single->title }}
                </h3>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $single->members_count }} member
                    </div>
                    <div class="text-xs text-slate-400 dark:text-slate-500">
                        #{{ $single->sequence }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
                <div class="text-slate-400 text-lg">Belum ada single yang tercatat.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
