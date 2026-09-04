@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="text-sm list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-5xl">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Sub-unit <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $subUnit->name ?? '') }}" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('description', $subUnit->description ?? '') }}</textarea>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-slate-700">Lagu Sub-unit</label>
            <button type="button" onclick="addSubUnitSongRow()" class="text-xs bg-slate-100 px-3 py-1 rounded hover:bg-slate-200">+ Baris</button>
        </div>
        <div id="song-rows" class="space-y-2">
            @php $seed = $songs->values(); @endphp
            @foreach ($seed as $i => $s)
                <div class="grid grid-cols-12 gap-2 items-start border border-slate-200 rounded p-2">
                    <input type="hidden" name="songs[{{ $i }}][id]" value="{{ $s->id }}">
                    <input class="col-span-3 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Judul" required
                           name="songs[{{ $i }}][title]" value="{{ $s->title }}">
                    <input class="col-span-3 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Judul Asal"
                           name="songs[{{ $i }}][title_original]" value="{{ $s->title_original }}">
                    <input class="col-span-1 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Asal"
                           name="songs[{{ $i }}][origin_group]" value="{{ $s->origin_group }}">
                    <input type="date" class="col-span-2 px-2 py-1 border border-slate-300 rounded text-sm"
                           name="songs[{{ $i }}][debut_date]" value="{{ $s->debut_date?->format('Y-m-d') }}">
                    <input class="col-span-2 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Debut di"
                           name="songs[{{ $i }}][debut_at]" value="{{ $s->debut_at }}">
                    <label class="text-xs col-span-1 flex items-center gap-1"><input type="checkbox" name="songs[{{ $i }}][released]" value="1" @checked($s->released)> Rilis</label>
                    <label class="text-xs col-span-1 flex items-center gap-1"><input type="checkbox" name="songs[{{ $i }}][has_mv]" value="1" @checked($s->has_mv)> MV</label>
                    <input class="col-span-11 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Preview URL (YouTube)"
                           name="songs[{{ $i }}][preview_url]" value="{{ $s->preview_url }}">
                    <button type="button" onclick="this.closest('.grid').remove()" class="col-span-1 text-xs text-red-600">Hapus</button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.sub-units.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>

<script>
    let subUnitSongIndex = {{ $seed->count() }};
    function addSubUnitSongRow() {
        const i = subUnitSongIndex++;
        const html = `
        <div class="grid grid-cols-12 gap-2 items-start border border-slate-200 rounded p-2">
            <input class="col-span-3 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Judul" required name="songs[${i}][title]">
            <input class="col-span-3 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Judul Asal" name="songs[${i}][title_original]">
            <input class="col-span-1 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Asal" name="songs[${i}][origin_group]">
            <input type="date" class="col-span-2 px-2 py-1 border border-slate-300 rounded text-sm" name="songs[${i}][debut_date]">
            <input class="col-span-2 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Debut di" name="songs[${i}][debut_at]">
            <label class="text-xs col-span-1 flex items-center gap-1"><input type="checkbox" name="songs[${i}][released]" value="1"> Rilis</label>
            <label class="text-xs col-span-1 flex items-center gap-1"><input type="checkbox" name="songs[${i}][has_mv]" value="1"> MV</label>
            <input class="col-span-11 px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Preview URL" name="songs[${i}][preview_url]">
            <button type="button" onclick="this.closest('.grid').remove()" class="col-span-1 text-xs text-red-600">Hapus</button>
        </div>`;
        document.getElementById('song-rows').insertAdjacentHTML('beforeend', html);
    }
</script>
