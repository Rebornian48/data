@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="text-sm list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-2xl">
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $single->code ?? '') }}" required
                   placeholder="S24" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Sequence <span class="text-red-500">*</span></label>
            <input type="number" name="sequence" value="{{ old('sequence', $single->sequence ?? '') }}" required min="1"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Judul (ID) <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $single->title ?? '') }}" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Judul JP</label>
        <input type="text" name="title_jp" value="{{ old('title_jp', $single->title_jp ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Asal Grup</label>
            <select name="origin_group" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="">—</option>
                @foreach (['AKB48', 'SKE48', 'NMB48', 'HKT48', 'NGT48', 'Original'] as $g)
                    <option value="{{ $g }}" @selected(old('origin_group', $single->origin_group ?? '') === $g)>{{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Rilis</label>
            <input type="date" name="release_date" value="{{ old('release_date', $single->release_date?->format('Y-m-d') ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Rilis</label>
            <input type="number" name="release_year" value="{{ old('release_year', $single->release_year ?? '') }}" min="2000" max="2100"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">MV Title</label>
        <input type="text" name="mv_title" value="{{ old('mv_title', $single->mv_title ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">MV URL (YouTube)</label>
        <input type="url" name="mv_url" value="{{ old('mv_url', $single->mv_url ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Cover Art URL</label>
        <input type="url" name="cover_art_url" value="{{ old('cover_art_url', $single->cover_art_url ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Audio File (nama file lokal)</label>
        <input type="text" name="audio_file" value="{{ old('audio_file', $single->audio_file ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('notes', $single->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.singles.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
