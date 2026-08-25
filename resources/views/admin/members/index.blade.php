@extends('layouts.admin')

@section('title', 'Member')
@section('page_title', 'Kelola Member')
@section('page_subtitle', 'Manajemen data seluruh member JKT48')

@section('content')
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <form method="GET" class="flex flex-1 gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama..."
                   class="flex-1 md:max-w-xs px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <select name="generation" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Semua Generasi</option>
                @foreach ($generations as $gen)
                    <option value="{{ $gen->id }}" @selected(request('generation') == $gen->id)>{{ $gen->code }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Semua</option>
                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                <option value="Lulus" @selected(request('status') == 'Lulus')>Lulus</option>
            </select>
            <button class="admin-filter-btn px-4 py-2 rounded-lg text-sm">Filter</button>
        </form>
        <a href="{{ route('admin.members.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 whitespace-nowrap">
            + Tambah Member
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm sortable">
            <thead class="text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Panggilan</th>
                    <th class="px-4 py-3 text-left">Generasi</th>
                    <th class="px-4 py-3 text-left">Bergabung</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right" data-nosort>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($members as $member)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $member->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $member->nickname ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded font-medium">
                                {{ $member->generation->code }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600" data-sort="{{ $member->join_date?->format('Y-m-d') ?? '' }}">{{ $member->join_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $member->status === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $member->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.members.edit', $member) }}"
                                   class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus {{ $member->name }}?')">
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
                        <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                            Tidak ada data member.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-200">
        {{ $members->links() }}
    </div>
</div>
@endsection
