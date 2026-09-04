@extends('layouts.admin')
@section('title', 'Setlist')
@section('page_title', 'Kelola Setlist')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $setlists->total() }} setlist.</div>
        <a href="{{ route('admin.setlists.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">+ Tambah Setlist</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">Tipe</th>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-center">Lagu</th>
                    <th class="px-3 py-2 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($setlists as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 uppercase text-xs">
                            <span class="px-2 py-0.5 rounded {{ $s->type === 'special' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $s->type }}
                            </span>
                        </td>
                        <td class="px-3 py-2 font-medium">{{ $s->name }}</td>
                        <td class="px-3 py-2 text-center font-semibold">{{ $s->songs_count }}</td>
                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.setlists.edit', $s) }}" class="px-3 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.setlists.destroy', $s) }}" onsubmit="return confirm('Hapus setlist?')">
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
    <div class="p-4 border-t border-slate-200">{{ $setlists->links() }}</div>
</div>
@endsection
