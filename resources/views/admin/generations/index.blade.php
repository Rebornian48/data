@extends('layouts.admin')

@section('page_title', 'Kelola Generasi')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $generations->count() }} generasi terdaftar.</div>
        <a href="{{ route('admin.generations.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
            + Tambah Generasi
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Kode</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                    <th class="px-4 py-3 text-center">Total Member</th>
                    <th class="px-4 py-3 text-center">Aktif</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($generations as $gen)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <span class="bg-red-100 text-brand text-xs px-2 py-0.5 rounded font-bold">
                                {{ $gen->code }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $gen->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $gen->join_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-semibold">{{ $gen->members_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-green-600 font-semibold">{{ $gen->active_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.generations.edit', $gen) }}" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.generations.destroy', $gen) }}" onsubmit="return confirm('Yakin?')">
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
