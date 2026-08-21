@extends('layouts.admin')

@section('page_title', 'Riwayat Kapten')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $captains->total() }} data kapten.</div>
        <a href="{{ route('admin.captains.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
            + Tambah Kapten
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Posisi</th>
                    <th class="px-4 py-3 text-left">Mulai</th>
                    <th class="px-4 py-3 text-left">Berakhir</th>
                    <th class="px-4 py-3 text-center">Durasi</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($captains as $captain)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $captain->member->name }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-red-50 text-brand text-xs px-2 py-0.5 rounded font-medium">
                                {{ $captain->position }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $captain->start_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $captain->end_date?->format('d M Y') ?? 'Sekarang' }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">
                            {{ number_format($captain->duration_days) }}
                            <span class="text-xs text-slate-500 font-normal">hari</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.captains.edit', $captain) }}" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">Edit</a>
                                <form method="POST" action="{{ route('admin.captains.destroy', $captain) }}" onsubmit="return confirm('Yakin?')">
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

    <div class="p-4 border-t border-slate-200">{{ $captains->links() }}</div>
</div>
@endsection
