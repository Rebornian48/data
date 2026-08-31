@csrf
@if ($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
    <ul class="text-sm list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif
<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-2xl">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode *</label>
            <input type="text" name="code" required maxlength="20" value="{{ old('code', $team->code ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
            <p class="text-xs text-slate-500 mt-1">Contoh: J, KIII, T.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama *</label>
            <input type="text" name="name" required maxlength="100" value="{{ old('name', $team->name ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Warna</label>
        <input type="text" name="color" maxlength="20" placeholder="#3b82f6" value="{{ old('color', $team->color ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Dibentuk</label>
            <input type="date" name="formed_at" value="{{ old('formed_at', $team->formed_at?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Dibubarkan</label>
            <input type="date" name="disbanded_at" value="{{ old('disbanded_at', $team->disbanded_at?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <p class="text-xs text-slate-500 mt-1">Kosongkan jika masih aktif.</p>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('notes', $team->notes ?? '') }}</textarea>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.teams.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
