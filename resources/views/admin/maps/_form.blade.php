@csrf
@if ($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
    <ul class="text-sm list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-3xl">
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $map->slug ?? '') }}"
                   placeholder="otomatis dari judul"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono text-sm">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul *</label>
            <input type="text" name="title" value="{{ old('title', $map->title ?? '') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Subjudul</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $map->subtitle ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Google Sheet ID</label>
        <input type="text" name="google_sheet_id" value="{{ old('google_sheet_id', $map->google_sheet_id ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono text-sm">
        <p class="text-xs text-slate-500 mt-1">Opsional. Hanya untuk link atribusi "Edit di Google Sheets".</p>
    </div>

    <div>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $map->is_published ?? true) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-slate-700">Publikasikan</span>
        </label>
    </div>
</div>

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-3 max-w-3xl mt-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm font-bold text-slate-800">Settings (Options)</div>
            <div class="text-xs text-slate-500">Pasangan key/value mirroring tab Options di Google Sheet.</div>
        </div>
        <button type="button" onclick="addSettingRow()" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">+ Baris</button>
    </div>

    <div id="settings-rows" class="space-y-2">
        @php $rows = old('settings', $settings ?? []); @endphp
        @forelse ($rows as $i => $row)
            <div class="grid grid-cols-12 gap-2 settings-row">
                <input type="text" name="settings[{{ $i }}][key]" value="{{ $row['key'] ?? '' }}" placeholder="key" class="col-span-4 px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono">
                <input type="text" name="settings[{{ $i }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="value" class="col-span-7 px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <button type="button" onclick="this.closest('.settings-row').remove()" class="col-span-1 px-2 py-2 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">X</button>
            </div>
        @empty
            <div class="text-xs text-slate-400">Belum ada settings.</div>
        @endforelse
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.maps.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>

<script>
    let settingsIdx = {{ count($rows ?? []) }};
    function addSettingRow() {
        const wrap = document.getElementById('settings-rows');
        const empty = wrap.querySelector('.text-slate-400');
        if (empty) empty.remove();
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 settings-row';
        row.innerHTML =
            '<input type="text" name="settings[' + settingsIdx + '][key]" placeholder="key" class="col-span-4 px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono">' +
            '<input type="text" name="settings[' + settingsIdx + '][value]" placeholder="value" class="col-span-7 px-3 py-2 border border-slate-300 rounded-lg text-sm">' +
            '<button type="button" class="col-span-1 px-2 py-2 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">X</button>';
        row.querySelector('button').addEventListener('click', () => row.remove());
        wrap.appendChild(row);
        settingsIdx++;
    }
</script>
