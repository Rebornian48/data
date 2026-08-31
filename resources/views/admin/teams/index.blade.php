@extends('layouts.admin')

@section('page_title', 'Master Tim')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $teams->count() }} tim.</div>
        <a href="{{ route('admin.teams.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
            + Tambah Tim
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Kode</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Warna</th>
                    <th class="px-4 py-3 text-left">Dibentuk</th>
                    <th class="px-4 py-3 text-left">Dibubarkan</th>
                    <th class="px-4 py-3 text-center">Riwayat Member</th>
                    <th class="px-4 py-3 text-center">Kapten</th>
                    <th class="px-4 py-3 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($teams as $team)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $team->code }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $team->name }}</td>
                        <td class="px-4 py-3">
                            @if ($team->color)
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-4 h-4 rounded" style="background: {{ $team->color }}; border:1px solid #000;"></span>
                                    <span class="text-xs text-slate-500">{{ $team->color }}</span>
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $team->formed_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $team->disbanded_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $team->memberships_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $team->captains_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.teams.edit', $team) }}" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Yakin hapus tim ini? Semua riwayat member dan kapten yang terkait akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
