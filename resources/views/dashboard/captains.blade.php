@extends('layouts.app')

@section('title', 'Captains - JKT48 Database')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">Captains</h1>
        <p class="text-slate-500 dark:text-slate-400">Riwayat kapten dan wakil kapten JKT48.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 mb-8">
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Timeline Kapten</h2>
        <div class="h-96">
            <canvas id="captainTimeline"></canvas>
        </div>
    </div>

    @foreach ($positions as $position => $items)
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 mb-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">{{ $position }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="text-left py-2 px-3 text-slate-500 dark:text-slate-400 font-medium">Nama</th>
                        <th class="text-left py-2 px-3 text-slate-500 dark:text-slate-400 font-medium">Generasi</th>
                        <th class="text-left py-2 px-3 text-slate-500 dark:text-slate-400 font-medium">Mulai</th>
                        <th class="text-left py-2 px-3 text-slate-500 dark:text-slate-400 font-medium">Selesai</th>
                        <th class="text-right py-2 px-3 text-slate-500 dark:text-slate-400 font-medium">Durasi</th>
                        <th class="text-center py-2 px-3 text-slate-500 dark:text-slate-400 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $captain)
                        <tr class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="py-2.5 px-3">
                                <a href="{{ route('members.show', $captain->member) }}" class="font-medium text-slate-900 dark:text-slate-100 hover:text-brand">
                                    {{ $captain->member->name }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 dark:text-slate-400">{{ $captain->member->generation->code }}</td>
                            <td class="py-2.5 px-3 text-slate-600 dark:text-slate-400">{{ $captain->start_date->format('d M Y') }}</td>
                            <td class="py-2.5 px-3 text-slate-600 dark:text-slate-400">
                                {{ $captain->end_date ? $captain->end_date->format('d M Y') : '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-medium text-slate-900 dark:text-slate-100">
                                {{ number_format($captain->duration_days) }} hari
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($captain->end_date === null)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

<script>
    const timelineData = {!! json_encode($timelineData) !!};
    const positionColors = {
        'Kapten JKT48': '#E60012',
        'Wakil Kapten JKT48': '#ff4d6d',
        'Kapten Tim J': '#3b82f6',
        'Kapten Tim KIII': '#8b5cf6',
        'Kapten Tim T': '#10b981',
    };
    const positions = [...new Set(timelineData.map(d => d.position))];
    const allDates = timelineData.flatMap(d => [new Date(d.start), new Date(d.end)]);
    const minDate = new Date(Math.min(...allDates));
    const maxDate = new Date(Math.max(...allDates));

    const datasets = positions.map((pos, i) => {
        const items = timelineData.filter(d => d.position === pos);
        return {
            label: pos,
            data: items.map(d => ({
                x: [new Date(d.start), new Date(d.end)],
                y: pos,
                member: d.member,
                active: d.active
            })),
            backgroundColor: positionColors[pos] || '#94a3b8',
            borderColor: positionColors[pos] || '#94a3b8',
            borderWidth: 8,
            borderRadius: 4,
            borderSkipped: false,
        };
    });

    const ctx = document.getElementById('captainTimeline');
    new Chart(ctx, {
        type: 'bar',
        data: { datasets },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: { unit: 'year', displayFormats: { year: 'yyyy' } },
                    min: minDate.toISOString(),
                    max: maxDate.toISOString(),
                    adapters: { date: {} }
                },
                y: {
                    type: 'category',
                    labels: positions,
                    offset: true
                }
            },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'rectRounded' } },
                tooltip: {
                    callbacks: {
                        title: function(items) {
                            const d = items[0].raw;
                            return d.member;
                        },
                        label: function(item) {
                            const d = item.raw;
                            const start = new Date(d.x[0]).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                            const end = new Date(d.x[1]).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                            return `${item.dataset.label}: ${start} - ${end}`;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
