@csrf
@if ($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
    <ul class="text-sm list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif
<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-2xl">
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode *</label>
            <input type="text" name="code" value="{{ old('code', $generation->code ?? '') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama *</label>
            <input type="text" name="name" value="{{ old('name', $generation->name ?? '') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Pengumuman</label>
            <input type="date" name="announcement_date" value="{{ old('announcement_date', $generation->announcement_date?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Masuk</label>
            <input type="date" name="join_date" value="{{ old('join_date', $generation->join_date?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('description', $generation->description ?? '') }}</textarea>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.generations.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
