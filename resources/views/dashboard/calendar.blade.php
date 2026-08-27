@extends('layouts.app')

@section('title', 'Kalender - JKT48 Database')

@section('content')
<style>
    .cal-wrap { max-width: 88rem; margin: 0 auto; padding: 2rem 1rem; }
    .cal-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .cal-title { font-family: 'Archivo Black', 'Space Grotesk', sans-serif; font-size: 2.25rem; line-height: 1; color: #831843; text-transform: uppercase; letter-spacing: .02em; margin: 0; }
    .cal-nav { display: flex; align-items: center; gap: .5rem; }
    .cal-btn {
        background: #fff; border: 3px solid #000; box-shadow: 3px 3px 0 #000;
        padding: .5rem .9rem; font-weight: 700; font-size: .875rem;
        text-decoration: none; color: #000;
    }
    .cal-btn:hover { background: #ffd23f; }
    .cal-select {
        border: 3px solid #000; box-shadow: 3px 3px 0 #000;
        padding: .5rem .75rem; font-weight: 700; background: #fff;
    }

    .cal-grid { width: 100%; border-collapse: separate; border-spacing: 0; border: 3px solid #000; background: #fff; box-shadow: 6px 6px 0 #000; }
    .cal-grid th {
        background: #fce7f3; color: #831843; text-transform: uppercase;
        font-weight: 800; font-size: .8rem; letter-spacing: .06em;
        padding: .6rem .5rem; border-bottom: 3px solid #000;
    }
    .cal-grid th.sun, .cal-grid td.sun { color: #dc2626; }
    .cal-grid th + th, .cal-grid td + td { border-left: 2px solid #000; }
    .cal-grid tr + tr td { border-top: 2px solid #000; }
    .cal-grid td {
        vertical-align: top; height: 8.5rem; padding: .35rem .4rem;
        background: #fff;
    }
    .cal-grid td.blank { background: #f8fafc; }
    .cal-grid td.today { background: #fff7ed; }
    .day-num { font-weight: 800; font-size: 1rem; margin-bottom: .25rem; }
    .day-num.sun { color: #dc2626; }
    .events { display: flex; flex-direction: column; gap: 2px; }
    .ev {
        display: block; font-size: .72rem; line-height: 1.15;
        padding: 2px 5px; border: 1.5px solid #000; border-radius: 4px;
        text-decoration: none; color: #000; word-break: break-word;
    }
    .ev:hover { filter: brightness(.95); }
    .ev.birthday { background: #fef08a; }
    .ev.gen { background: #bae6fd; font-weight: 700; }
    .ev.graduate { background: #fecaca; }

    .legend { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem; font-size: .8rem; font-weight: 600; }
    .legend-chip {
        display: inline-flex; align-items: center; gap: .4rem;
    }
    .legend-swatch {
        width: 14px; height: 14px; border: 1.5px solid #000; border-radius: 3px;
    }

    @media (max-width: 768px) {
        .cal-title { font-size: 1.6rem; }
        .cal-grid td { height: 6rem; padding: .2rem .25rem; }
        .cal-grid th { font-size: .65rem; padding: .35rem .2rem; }
        .day-num { font-size: .8rem; }
        .ev { font-size: .6rem; padding: 1px 3px; }
    }
</style>

<div class="cal-wrap">
    <div class="cal-header">
        <h1 class="cal-title">Kalender &middot; {{ $monthName }} {{ $year }}</h1>
        <div class="cal-nav">
            <a href="{{ route('calendar.index', ['y' => $prev->year, 'm' => $prev->month]) }}" class="cal-btn">&laquo; {{ $monthNames[$prev->month] }}</a>
            <form method="GET" action="{{ route('calendar.index') }}" class="flex items-center gap-2">
                <select name="m" class="cal-select" onchange="this.form.submit()">
                    @foreach ($monthNames as $num => $name)
                        <option value="{{ $num }}" @selected($num === $month)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="y" class="cal-select" onchange="this.form.submit()">
                    @for ($y = 2011; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <a href="{{ route('calendar.index') }}" class="cal-btn">Hari Ini</a>
            <a href="{{ route('calendar.index', ['y' => $next->year, 'm' => $next->month]) }}" class="cal-btn">{{ $monthNames[$next->month] }} &raquo;</a>
        </div>
    </div>

    <table class="cal-grid">
        <thead>
            <tr>
                <th class="sun">Minggu</th>
                <th>Senin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Kamis</th>
                <th>Jumat</th>
                <th>Sabtu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($weeks as $week)
                <tr>
                    @foreach ($week as $col => $d)
                        @php
                            $isSun = $col === 0;
                            $dateStr = $d ? sprintf('%04d-%02d-%02d', $year, $month, $d) : null;
                            $dayEvents = $dateStr ? ($events[$dateStr] ?? []) : [];
                            $isToday = $dateStr === $today;
                        @endphp
                        <td class="{{ $d === null ? 'blank' : '' }} {{ $isSun ? 'sun' : '' }} {{ $isToday ? 'today' : '' }}">
                            @if ($d !== null)
                                <div class="day-num {{ $isSun ? 'sun' : '' }}">{{ $d }}</div>
                                @if (count($dayEvents))
                                    <div class="events">
                                        @foreach ($dayEvents as $ev)
                                            @if ($ev['url'])
                                                <a href="{{ $ev['url'] }}" class="ev {{ $ev['type'] }}" title="{{ $ev['label'] }}">{{ $ev['label'] }}</a>
                                            @else
                                                <span class="ev {{ $ev['type'] }}" title="{{ $ev['label'] }}">{{ $ev['label'] }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <span class="legend-chip"><span class="legend-swatch" style="background:#fef08a"></span> Ulang tahun member</span>
        <span class="legend-chip"><span class="legend-swatch" style="background:#bae6fd"></span> Generasi masuk</span>
        <span class="legend-chip"><span class="legend-swatch" style="background:#fecaca"></span> Member lulus</span>
    </div>
</div>
@endsection
