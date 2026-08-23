@extends('layouts.app')

@section('title', 'Sorter - JKT48 Database')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">JKT48 Sorter</h1>
        <p class="text-slate-500 dark:text-slate-400">Urutkan berbagai koleksi JKT48 lewat perbandingan berpasangan (merge sort interaktif).</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('sorter.show', 'member') }}"
           class="group bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 hover:shadow-lg hover:-translate-y-0.5 transition">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 gradient-brand rounded-lg flex items-center justify-center text-white text-xl font-bold shrink-0">M</div>
                <div>
                    <div class="font-semibold text-slate-900 dark:text-slate-100 group-hover:text-brand transition">Member Sorter</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Urutkan member JKT48 favoritmu. Filter per generasi &amp; status.</div>
                </div>
            </div>
        </a>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-dashed border-slate-300 dark:border-slate-700 opacity-70">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 text-xl font-bold shrink-0">?</div>
                <div>
                    <div class="font-semibold text-slate-700 dark:text-slate-300">Segera hadir</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sorter lagu, setlist, dan koleksi lain akan menyusul.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
