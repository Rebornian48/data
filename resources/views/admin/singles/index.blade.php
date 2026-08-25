@extends('layouts.admin')

@section('title', 'Single')
@section('page_title', 'Kelola Single')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="text-sm text-slate-500">{{ $singles->total() }} single tersimpan.</div>
        <a href="{{ route('admin.singles.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
            + Tambah Single
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Kode</th>
                    <th class="px-4 py-3 text-left">Judul</th>
                    <th class="px-4 py-3 text-left">Rilis</th>
                    <th class="px-4 py-3 text-center">Senbatsu</th>
                    <th class="px-4 py-3 text-center">Center</th>
                    <th class="px-4 py-3 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($singles as $single)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded font-mono font-medium">
                                {{ $single->code }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $single->title }}</td>
                        <td class="px-4 py-3 text-slate-600" data-sort="{{ $single->release_date?->format('Y-m-d') ?? '' }}">{{ $single->release_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-slate-900">{{ $single->members_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($single->center_count > 0)
                                <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-semibold">
                                    {{ $single->center_count }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.singles.edit', $single) }}" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.singles.destroy', $single) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus single ini?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-500">Belum ada data single.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-200">
        {{ $singles->links() }}
    </div>
</div>
@endsection
