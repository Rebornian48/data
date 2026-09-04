@extends('layouts.admin')
@section('title', 'Album / EP')
@section('page_title', 'Kelola Album / EP')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $albums->total() }} album/EP.</div>
        <a href="{{ route('admin.albums.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">+ Tambah Album</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">Kode</th>
                    <th class="px-3 py-2 text-left">Tipe</th>
                    <th class="px-3 py-2 text-left">Judul</th>
                    <th class="px-3 py-2 text-left">Rilis</th>
                    <th class="px-3 py-2 text-center">Track</th>
                    <th class="px-3 py-2 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($albums as $album)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 font-mono text-xs">{{ $album->code }}</td>
                        <td class="px-3 py-2 uppercase text-xs">{{ $album->type }}</td>
                        <td class="px-3 py-2 font-medium">
                            {{ $album->title }}
                            @if ($album->title_jp)
                                <span class="text-slate-400 italic text-xs">({{ $album->title_jp }})</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-500" data-sort="{{ $album->release_date?->format('Y-m-d') ?? '' }}">{{ $album->release_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-3 py-2 text-center font-semibold">{{ $album->tracks_count }}</td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.albums.edit', $album) }}" class="px-3 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.albums.destroy', $album) }}" onsubmit="return confirm('Hapus album ini beserta tracklist?')">
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
    <div class="p-4 border-t border-slate-200">{{ $albums->links() }}</div>
</div>
@endsection
