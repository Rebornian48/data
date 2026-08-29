@extends('layouts.admin')

@section('page_title', 'Kelola Peta')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $maps->count() }} peta terdaftar.</div>
        <a href="{{ route('admin.maps.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
            + Tambah Peta
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Slug</th>
                    <th class="px-4 py-3 text-left">Judul</th>
                    <th class="px-4 py-3 text-left">Subjudul</th>
                    <th class="px-4 py-3 text-center">Publish</th>
                    <th class="px-4 py-3 text-center">Points</th>
                    <th class="px-4 py-3 text-center">Polylines</th>
                    <th class="px-4 py-3 text-center">Polygons</th>
                    <th class="px-4 py-3 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($maps as $map)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $map->slug }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $map->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $map->subtitle ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($map->is_published)
                                <span class="text-green-600 font-semibold">YA</span>
                            @else
                                <span class="text-slate-400 font-semibold">TIDAK</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">{{ $map->points_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $map->polylines_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $map->polygon_layers_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('peta.show', $map->slug) }}" target="_blank" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">Lihat</a>
                                <a href="{{ route('admin.maps.edit', $map) }}" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.maps.destroy', $map) }}" onsubmit="return confirm('Yakin hapus peta ini beserta semua data terkait?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">Belum ada peta. Jalankan <code>php artisan db:seed --class=Database\\Seeders\\JKT48MapSeeder</code> atau tambah manual.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
