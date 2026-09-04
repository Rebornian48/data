@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="text-sm list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-3xl">
    <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $setlist->name ?? '') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tipe <span class="text-red-500">*</span></label>
            <select name="type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="regular" @selected(old('type', $setlist->type ?? 'regular') === 'regular')>Reguler</option>
                <option value="special" @selected(old('type', $setlist->type ?? '') === 'special')>Special</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('description', $setlist->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Lagu <span class="text-xs text-slate-500">(Ctrl/Cmd + klik untuk multi-pilih; urutan pilih = posisi)</span></label>
        <select name="song_ids[]" multiple size="16" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            @foreach ($songs as $s)
                <option value="{{ $s->id }}" @selected(in_array($s->id, old('song_ids', $selectedSongs)))>
                    {{ $s->title }}
                    @if ($s->title_original && $s->title_original !== $s->title) — {{ $s->title_original }} @endif
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.setlists.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
