@extends('layouts.admin')
@section('title', 'Lagu')
@section('page_title', 'Kelola Lagu')

@section('content')
<form method="GET" class="mb-4 flex flex-wrap gap-2 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul..."
           class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
    <select name="single_id" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
        <option value="">— semua single —</option>
        @foreach ($singles as $s)
            <option value="{{ $s->id }}" @selected(request('single_id') == $s->id)>{{ $s->code }} — {{ $s->title }}</option>
        @endforeach
    </select>
    <select name="origin_group" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
        <option value="">— semua asal —</option>
        @foreach ($groups as $g)
            <option value="{{ $g }}" @selected(request('origin_group') === $g)>{{ $g }}</option>
        @endforeach
    </select>
    <button class="admin-filter-btn px-4 py-2 text-sm">Filter</button>
    <a href="{{ route('admin.songs.index') }}" class="text-sm text-slate-500">Reset</a>
</form>

<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $songs->total() }} lagu.</div>
        <a href="{{ route('admin.songs.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">+ Tambah Lagu</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">ID</th>
                    <th class="px-3 py-2 text-left">Judul</th>
                    <th class="px-3 py-2 text-left">Judul Asal</th>
                    <th class="px-3 py-2 text-left">Asal</th>
                    <th class="px-3 py-2 text-left">Single</th>
                    <th class="px-3 py-2 text-center">Rilis</th>
                    <th class="px-3 py-2 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($songs as $song)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-500">#{{ $song->external_id }}</td>
                        <td class="px-3 py-2 font-medium">{{ $song->title }}</td>
                        <td class="px-3 py-2 text-slate-500 italic">{{ $song->title_original ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-500">{{ $song->origin_group ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-500">{{ $song->single?->code ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">
                            @if ($song->released)
                                <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">Ya</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">Belum</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.songs.edit', $song) }}" class="px-3 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.songs.destroy', $song) }}" onsubmit="return confirm('Hapus lagu ini?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Tidak ada lagu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-200">{{ $songs->links() }}</div>
</div>
@endsection
