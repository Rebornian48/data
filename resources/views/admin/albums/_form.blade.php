@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="text-sm list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-3xl">
    <div class="grid grid-cols-4 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $album->code ?? '') }}" required
                   placeholder="album-1 / ep-1" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tipe <span class="text-red-500">*</span></label>
            <select name="type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="album" @selected(old('type', $album->type ?? 'album') === 'album')>Album Studio</option>
                <option value="ep" @selected(old('type', $album->type ?? '') === 'ep')>Mini Album / EP</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sequence <span class="text-red-500">*</span></label>
            <input type="number" name="sequence" value="{{ old('sequence', $album->sequence ?? '') }}" required min="1"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Rilis</label>
            <input type="date" name="release_date" value="{{ old('release_date', $album->release_date?->format('Y-m-d') ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $album->title ?? '') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul JP</label>
            <input type="text" name="title_jp" value="{{ old('title_jp', $album->title_jp ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Cover URL</label>
        <input type="url" name="cover_url" value="{{ old('cover_url', $album->cover_url ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tracklist</label>
        <p class="text-xs text-slate-500 mb-1">Satu baris = satu lagu. Format: <code>Judul</code> atau <code>Judul || song_id</code>. Urutan baris = posisi.</p>
        @php
            $tracksRaw = old('tracks', isset($album->tracks) ? $album->tracks->map(fn ($t) => $t->title . ($t->song_id ? ' || ' . $t->song_id : ''))->implode("\n") : '');
        @endphp
        <textarea name="tracks" rows="12" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono text-sm">{{ $tracksRaw }}</textarea>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.albums.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
