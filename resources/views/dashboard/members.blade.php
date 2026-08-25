@extends('layouts.app')

@section('title', 'Member - JKT48 Database')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Member</h1>
        <p class="text-slate-500 dark:text-slate-400">Semua member JKT48 dari generasi ke generasi.</p>
    </div>

    <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, panggilan, atau kota kelahiran..."
                       class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
            <select name="generation" class="px-4 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Semua Generasi</option>
                @foreach ($generations as $gen)
                    <option value="{{ $gen->id }}" @selected(request('generation') == $gen->id)>{{ $gen->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Semua Status</option>
                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                <option value="Lulus" @selected(request('status') == 'Lulus')>Lulus</option>
            </select>
        </div>
        <div class="mt-3 flex gap-2">
            <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                Filter
            </button>
            <a href="{{ route('members.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-4 py-2 text-sm">
                Reset
            </a>
        </div>
    </form>

    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
        Menampilkan {{ $members->count() }} dari {{ $members->total() }} member.
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse ($members as $member)
            <a href="{{ route('members.show', $member) }}"
               class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition">
                <div class="aspect-square bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center">
                    @if ($member->photo_url)
                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-20 h-20 gradient-brand rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ strtoupper(substr($member->nickname ?: $member->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm truncate">{{ $member->name }}</div>
                    @if ($member->nickname)
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $member->nickname }}</div>
                    @endif
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded">
                            {{ $member->generation->code }}
                        </span>
                        <span class="text-xs font-medium {{ $member->status === 'Aktif' ? 'text-green-600 dark:text-green-400' : 'text-slate-500 dark:text-slate-400' }}">
                            {{ $member->status }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
                <div class="text-slate-400 text-lg">Tidak ada member yang cocok dengan filter.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $members->links() }}
    </div>
</div>
@endsection
