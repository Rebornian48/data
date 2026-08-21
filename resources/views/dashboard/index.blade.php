@extends('layouts.app')

@section('title', 'Dashboard - JKT48 Database')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Hero --}}
    <div class="gradient-brand rounded-2xl p-8 md:p-12 text-white mb-8 relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-3">JKT48 Database</h1>
            <p class="text-white/90 text-lg max-w-2xl">
                Katalog lengkap seluruh member JKT48 sejak 2011, termasuk data karier, single, dan sejarah kapten.
            </p>
        </div>
        <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-white/10 rounded-full"></div>
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        @foreach ([
            ['label' => 'Total Member', 'value' => $stats['total_members'], 'color' => 'slate', 'icon' => 'users'],
            ['label' => 'Member Aktif', 'value' => $stats['active_members'], 'color' => 'green', 'icon' => 'check'],
            ['label' => 'Sudah Lulus', 'value' => $stats['graduated_members'], 'color' => 'blue', 'icon' => 'graduate'],
            ['label' => 'Total Generasi', 'value' => $stats['total_generations'], 'color' => 'purple', 'icon' => 'stack'],
            ['label' => 'Total Single', 'value' => $stats['total_singles'], 'color' => 'amber', 'icon' => 'music'],
        ] as $stat)
            <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition">
                <div class="text-sm text-slate-500 mb-1">{{ $stat['label'] }}</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stat['value']) }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Members per Generation Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Member per Generasi</h2>
                <span class="text-xs text-slate-500">Aktif vs Lulus</span>
            </div>
            <div class="h-72">
                <canvas id="genChart"></canvas>
            </div>
        </div>

        {{-- Age Distribution --}}
        <div class="bg-white rounded-xl p-6 border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Distribusi Usia (Aktif)</h2>
            <div class="h-72">
                <canvas id="ageChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Active Captains --}}
    @if ($activeCaptains->count() > 0)
    <div class="bg-white rounded-xl p-6 border border-slate-200 mb-8">
        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span>Kapten Saat Ini</span>
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Active</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($activeCaptains as $captain)
                <div class="border border-slate-200 rounded-lg p-4 hover:border-red-300 transition">
                    <div class="text-xs font-medium text-brand mb-1">{{ $captain->position }}</div>
                    <a href="{{ route('members.show', $captain->member) }}" class="font-semibold text-slate-900 hover:text-brand">
                        {{ $captain->member->name }}
                    </a>
                    <div class="text-xs text-slate-500 mt-1">
                        Sejak {{ $captain->start_date->format('d M Y') }}
                        &middot; {{ number_format($captain->duration_days) }} hari
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top Tenure --}}
        <div class="bg-white rounded-xl p-6 border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Top 10 - Tenure Terlama</h2>
            <div class="space-y-2">
                @foreach ($longestTenure as $i => $member)
                    <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
                        <div class="w-8 h-8 rounded-full {{ $i < 3 ? 'bg-brand text-white' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-sm font-bold">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-slate-900 truncate">{{ $member->name }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $member->generation->name }}
                                &middot; {{ $member->status }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-brand">{{ number_format($member->days_in_jkt48) }}</div>
                            <div class="text-xs text-slate-500">hari</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Top Senbatsu --}}
        <div class="bg-white rounded-xl p-6 border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Top 10 - Senbatsu Terbanyak</h2>
            <div class="space-y-2">
                @foreach ($topSenbatsu as $i => $member)
                    <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
                        <div class="w-8 h-8 rounded-full {{ $i < 3 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-sm font-bold">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-slate-900 truncate">{{ $member->name }}</div>
                            <div class="text-xs text-slate-500">{{ $member->generation->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-amber-600">{{ $member->singles_count }}</div>
                            <div class="text-xs text-slate-500">single</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Center --}}
        <div class="bg-white rounded-xl p-6 border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Top Center</h2>
            <div class="space-y-2">
                @foreach ($topCenter->take(8) as $i => $member)
                    @if ($member->center_count > 0)
                    <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-slate-900 truncate">{{ $member->name }}</div>
                            <div class="text-xs text-slate-500">{{ $member->generation->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-purple-600">{{ $member->center_count }}x</div>
                            <div class="text-xs text-slate-500">center</div>
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Birth Places --}}
        <div class="bg-white rounded-xl p-6 border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Kota Kelahiran Terbanyak</h2>
            <div class="space-y-3">
                @php $maxBirth = $birthPlaces->max('total') ?: 1; @endphp
                @foreach ($birthPlaces as $bp)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-700">{{ $bp->birth_place }}</span>
                            <span class="font-semibold text-slate-900">{{ $bp->total }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="gradient-brand h-2 rounded-full" style="width: {{ ($bp->total / $maxBirth) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    // Members per Generation
    const genCtx = document.getElementById('genChart');
    new Chart(genCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($membersByGeneration->pluck('code')) !!},
            datasets: [
                {
                    label: 'Aktif',
                    data: {!! json_encode($membersByGeneration->pluck('active_count')) !!},
                    backgroundColor: '#10b981',
                },
                {
                    label: 'Lulus',
                    data: {!! json_encode($membersByGeneration->pluck('graduated_count')) !!},
                    backgroundColor: '#94a3b8',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true }
            },
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Age distribution
    const ageData = {!! json_encode($ageDistribution) !!};
    const ageCtx = document.getElementById('ageChart');
    new Chart(ageCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(ageData),
            datasets: [{
                data: Object.values(ageData),
                backgroundColor: ['#fca5a5', '#fda4af', '#f9a8d4', '#c084fc', '#a78bfa'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endsection
