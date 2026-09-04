@extends('layouts.admin')
@section('title', 'Sub-unit')
@section('page_title', 'Kelola Sub-unit')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $subUnits->count() }} sub-unit.</div>
        <a href="{{ route('admin.sub-units.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">+ Tambah Sub-unit</a>
    </div>
    <table class="w-full text-sm sortable">
        <thead class="text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Nama</th>
                <th class="px-3 py-2 text-center">Lagu</th>
                <th class="px-3 py-2 text-right" data-nosort>Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($subUnits as $u)
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2 font-medium">{{ $u->name }}</td>
                    <td class="px-3 py-2 text-center font-semibold">{{ $u->songs_count }}</td>
                    <td class="px-3 py-2 text-right">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('admin.sub-units.edit', $u) }}" class="px-3 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Edit</a>
                            <form method="POST" action="{{ route('admin.sub-units.destroy', $u) }}" onsubmit="return confirm('Hapus sub-unit beserta lagunya?')">
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
@endsection
