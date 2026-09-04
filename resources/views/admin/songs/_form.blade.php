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
            <label class="block text-sm font-medium text-slate-700 mb-1">External ID <span class="text-red-500">*</span></label>
            <input type="number" name="external_id" value="{{ old('external_id', $song->external_id ?? '') }}" required min="1"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div class="col-span-3">
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $song->title ?? '') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Judul Asal (JP / EN)</label>
        <input type="text" name="title_original" value="{{ old('title_original', $song->title_original ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Asal Grup</label>
            <select name="origin_group" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="">—</option>
                @foreach ($groups as $g)
                    <option value="{{ $g }}" @selected(old('origin_group', $song->origin_group ?? '') === $g)>{{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Single</label>
            <select name="single_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="">—</option>
                @foreach ($singles as $s)
                    <option value="{{ $s->id }}" @selected(old('single_id', $song->single_id ?? '') == $s->id)>{{ $s->code }} — {{ $s->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Debut</label>
            <input type="date" name="debut_date" value="{{ old('debut_date', $song->debut_date?->format('Y-m-d') ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Debut di</label>
            <input type="text" name="debut_at" value="{{ old('debut_at', $song->debut_at ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Setlist</label>
            <input type="text" name="setlist" value="{{ old('setlist', $song->setlist ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Special Setlist</label>
            <input type="text" name="special_setlist" value="{{ old('special_setlist', $song->special_setlist ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Preview URL (YouTube)</label>
            <input type="url" name="preview_url" value="{{ old('preview_url', $song->preview_url ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">MV Title</label>
            <input type="text" name="mv_title" value="{{ old('mv_title', $song->mv_title ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Other Compilations</label>
        <input type="text" name="other_compilations" value="{{ old('other_compilations', $song->other_compilations ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="hidden" name="released" value="0">
            <input type="checkbox" name="released" value="1" @checked(old('released', $song->released ?? true))>
            Sudah dirilis
        </label>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.songs.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
