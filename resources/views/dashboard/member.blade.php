@extends('layouts.app')

@section('title', $member->name . ' - JKT48 Database')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('members.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand mb-4 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="aspect-square gradient-brand flex items-center justify-center">
                    @if ($member->photo_url)
                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-white text-8xl font-black">
                            {{ strtoupper(substr($member->nickname ?: $member->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="text-xs text-brand font-semibold uppercase tracking-wider mb-1">
                        {{ $member->generation->name }}
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $member->name }}</h1>
                    @if ($member->nickname)
                        <div class="text-lg text-slate-500 dark:text-slate-400 mb-3">"{{ $member->nickname }}"</div>
                    @endif

                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $member->status === 'Aktif' ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $member->status === 'Aktif' ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                            {{ $member->status }}
                        </span>
                    </div>

                    <dl class="space-y-2 text-sm">
                        @if ($member->birth_place || $member->birth_date)
                            <div class="flex justify-between">
                                <dt class="text-slate-500 dark:text-slate-400">Lahir</dt>
                                <dd class="text-slate-900 dark:text-slate-100 text-right">
                                    {{ $member->birth_place }}<br>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $member->birth_date?->format('d M Y') }}</span>
                                </dd>
                            </div>
                        @endif
                        @if ($member->current_age)
                            <div class="flex justify-between">
                                <dt class="text-slate-500 dark:text-slate-400">Umur</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $member->current_age }} tahun</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 text-center">
                    <div class="text-3xl font-bold text-brand">{{ $member->years_in_jkt48 ?? '-' }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tahun di JKT48</div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 text-center">
                    <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $member->totalSenbatsu() }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Senbatsu</div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 text-center">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $member->totalCenter() }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Center</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Timeline Karier</h2>
                <div class="space-y-4">
                    @php
                        $events = [];
                        if ($member->join_date) $events[] = ['date' => $member->join_date, 'label' => 'Bergabung dengan JKT48', 'color' => 'green'];
                        if ($member->cancelled_date) $events[] = ['date' => $member->cancelled_date, 'label' => 'Dibatalkan', 'color' => 'red'];
                        if ($member->rejoin_date) $events[] = ['date' => $member->rejoin_date, 'label' => 'Masuk kembali', 'color' => 'blue'];
                        if ($member->promotion_date) $events[] = ['date' => $member->promotion_date, 'label' => 'Promosi ke Tim Inti', 'color' => 'purple'];
                        if ($member->graduation_announce_date) $events[] = ['date' => $member->graduation_announce_date, 'label' => 'Mengumumkan kelulusan' . ($member->graduation_announce_event ? " di {$member->graduation_announce_event}" : ''), 'color' => 'amber'];
                        if ($member->graduation_date) $events[] = ['date' => $member->graduation_date, 'label' => 'Lulus dari JKT48', 'color' => 'slate'];
                        usort($events, fn ($a, $b) => $a['date'] <=> $b['date']);
                    @endphp

                    @foreach ($events as $event)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-{{ $event['color'] }}-500"></div>
                                @if (! $loop->last)
                                    <div class="w-0.5 flex-1 bg-slate-200 dark:bg-slate-600 mt-1"></div>
                                @endif
                            </div>
                            <div class="pb-4 flex-1">
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $event['date']->format('d M Y') }}</div>
                                <div class="text-slate-900 dark:text-slate-100 font-medium">{{ $event['label'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($member->singles->count() > 0)
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Partisipasi Single</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($member->singles as $single)
                            <div class="flex items-center gap-3 p-2 rounded-lg bg-slate-50 dark:bg-slate-700">
                                <div class="text-xs font-bold text-slate-400 dark:text-slate-500 w-10">{{ $single->code }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{{ $single->title }}</div>
                                </div>
                                @if ($single->pivot->role === 'center')
                                    <span class="text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 px-2 py-0.5 rounded-full font-medium">Center</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($member->captains->count() > 0)
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Riwayat Kapten</h2>
                    <div class="space-y-2">
                        @foreach ($member->captains->sortBy('start_date') as $captain)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700">
                                <div>
                                    <div class="font-medium text-slate-900 dark:text-slate-100">{{ $captain->position }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $captain->start_date->format('d M Y') }}
                                        &mdash;
                                        {{ $captain->end_date ? $captain->end_date->format('d M Y') : 'Sekarang' }}
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-brand">
                                    {{ number_format($captain->duration_days) }} hari
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
