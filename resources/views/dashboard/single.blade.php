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
        <div class="flex items-center gap-3 mb-3">
            <span class="text-xs font-bold bg-brand text-white px-2.5 py-1 rounded-full">
                {{ $single->code }}
            </span>
            <span class="text-xs text-slate-400 dark:text-slate-500">#{{ $single->sequence }}</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-2">{{ $single->title }}</h1>
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Tanggal Rilis:
            <span class="font-medium text-slate-700 dark:text-slate-300">
                {{ $single->release_date?->format('d M Y') ?? ($single->notes ?? 'TBD') }}
            </span>
        </div>
        <div class="mt-3 text-sm text-slate-500 dark:text-slate-400">
            Total senbatsu: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $centers->count() + $senbatsu->count() }} member</span>
            @if ($centers->count())
                &middot; Center: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $centers->count() }} member</span>
            @endif
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
        <div>
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

    @if ($centers->isEmpty() && $senbatsu->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
            <div class="text-slate-400 text-lg">Belum ada data senbatsu untuk single ini.</div>
        </div>
    @endif
</div>
@endsection
