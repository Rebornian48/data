@extends('layouts.admin')
@section('title', 'Coupling Song')
@section('page_title', 'Kelola Coupling Song')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $rows->total() }} coupling song.</div>
        <a href="{{ route('admin.coupling-songs.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">+ Tambah Coupling</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">Single</th>
                    <th class="px-3 py-2 text-left">Judul</th>
                    <th class="px-3 py-2 text-left">Asal</th>
                    <th class="px-3 py-2 text-left">Tahun</th>
                    <th class="px-3 py-2 text-center">Member</th>
                    <th class="px-3 py-2 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($rows as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 font-mono text-xs">{{ $row->single?->code ?? '—' }}</td>
                        <td class="px-3 py-2 font-medium">
                            {{ $row->title }}
                            @if ($row->title_jp)
                                <span class="text-slate-400 italic text-xs">({{ $row->title_jp }})</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-500">{{ $row->origin_group ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-500">{{ $row->release_year ?? '—' }}</td>
                        <td class="px-3 py-2 text-center font-semibold">{{ $row->members_count }}</td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.coupling-songs.edit', $row) }}" class="px-3 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.coupling-songs.destroy', $row) }}" onsubmit="return confirm('Hapus coupling ini?')">
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
    <div class="p-4 border-t border-slate-200">{{ $rows->links() }}</div>
</div>
@endsection
