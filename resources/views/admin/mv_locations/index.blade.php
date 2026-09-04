@extends('layouts.admin')
@section('title', 'Lokasi MV')
@section('page_title', 'Kelola Lokasi Syuting MV')

@section('content')
<form method="GET" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari lagu / lokasi..."
           class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
    <button class="admin-filter-btn px-4 py-2 text-sm">Filter</button>
</form>

<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $rows->total() }} lokasi.</div>
        <a href="{{ route('admin.mv-locations.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">+ Tambah Lokasi</a>
    </div>
    <table class="w-full text-sm sortable">
        <thead class="text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Kategori</th>
                <th class="px-3 py-2 text-left">Lagu</th>
                <th class="px-3 py-2 text-left">Tahun</th>
                <th class="px-3 py-2 text-left">Lokasi</th>
                <th class="px-3 py-2 text-left">Koordinat</th>
                <th class="px-3 py-2 text-right" data-nosort>Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($rows as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2 text-slate-500">{{ $row->category ?? '—' }}</td>
                    <td class="px-3 py-2 font-medium">{{ $row->song_title }}</td>
                    <td class="px-3 py-2 text-slate-500">{{ $row->release_year ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $row->location }}</td>
                    <td class="px-3 py-2 text-xs font-mono {{ $row->latitude ? '' : 'text-slate-400' }}">
                        {{ $row->latitude ? $row->latitude.', '.$row->longitude : 'belum di-geocode' }}
                    </td>
                    <td class="px-3 py-2 text-right">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('admin.mv-locations.edit', $row) }}" class="px-3 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Edit</a>
                            <form method="POST" action="{{ route('admin.mv-locations.destroy', $row) }}" onsubmit="return confirm('Hapus lokasi?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-200">{{ $rows->links() }}</div>
</div>
@endsection
