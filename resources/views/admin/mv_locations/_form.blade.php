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
            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $row->category ?? '') }}"
                   placeholder="Single / Album Studio / Sub Unit" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
            <input type="number" name="release_year" value="{{ old('release_year', $row->release_year ?? '') }}" min="2000" max="2100"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Position</label>
            <input type="number" name="position" value="{{ old('position', $row->position ?? 1) }}" min="1"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Judul Lagu <span class="text-red-500">*</span></label>
        <input type="text" name="song_title" value="{{ old('song_title', $row->song_title ?? '') }}" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Judul JP</label>
        <input type="text" name="song_title_jp" value="{{ old('song_title_jp', $row->song_title_jp ?? '') }}"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
        <input type="text" name="location" value="{{ old('location', $row->location ?? '') }}" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Latitude</label>
            <input type="number" step="0.000001" name="latitude" value="{{ old('latitude', $row->latitude ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Longitude</label>
            <input type="number" step="0.000001" name="longitude" value="{{ old('longitude', $row->longitude ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('notes', $row->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.mv-locations.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
