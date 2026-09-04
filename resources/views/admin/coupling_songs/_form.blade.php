@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="text-sm list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-4xl">
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Single <span class="text-red-500">*</span></label>
            <select name="single_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="">—</option>
                @foreach ($singles as $s)
                    <option value="{{ $s->id }}" @selected(old('single_id', $row->single_id ?? '') == $s->id)>{{ $s->code }} — {{ $s->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $row->title ?? '') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul JP</label>
            <input type="text" name="title_jp" value="{{ old('title_jp', $row->title_jp ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Asal</label>
                <select name="origin_group" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="">—</option>
                    @foreach ($groups as $g)
                        <option value="{{ $g }}" @selected(old('origin_group', $row->origin_group ?? '') === $g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
                <input type="number" name="release_year" value="{{ old('release_year', $row->release_year ?? '') }}" min="2000" max="2100"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">MV Title</label>
            <input type="text" name="mv_title" value="{{ old('mv_title', $row->mv_title ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">MV URL</label>
            <input type="url" name="mv_url" value="{{ old('mv_url', $row->mv_url ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Audio File</label>
            <input type="text" name="audio_file" value="{{ old('audio_file', $row->audio_file ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 pt-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Member (pilih semua yang tampil di lagu ini)</label>
            <p class="text-xs text-slate-500 mb-1">Urutan klik = position 1..N. Gunakan Ctrl/Cmd+klik.</p>
            <select name="member_ids[]" multiple size="16" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                @foreach ($members as $m)
                    <option value="{{ $m->id }}" @selected(in_array($m->id, old('member_ids', $selectedMembers)))>
                        {{ $m->name }}@if ($m->nickname) ({{ $m->nickname }})@endif
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Center</label>
            <p class="text-xs text-slate-500 mb-1">Boleh lebih dari satu. Center di luar list Member tetap akan ditambahkan.</p>
            <select name="center_ids[]" multiple size="16" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                @foreach ($members as $m)
                    <option value="{{ $m->id }}" @selected(in_array($m->id, old('center_ids', $selectedCenters)))>
                        {{ $m->name }}@if ($m->nickname) ({{ $m->nickname }})@endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.coupling-songs.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
