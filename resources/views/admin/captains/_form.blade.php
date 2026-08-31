@csrf
@if ($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
    <ul class="text-sm list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif
<div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-2xl">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Member *</label>
        <select name="member_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            @foreach ($members as $m)
                <option value="{{ $m->id }}" @selected(old('member_id', $captain->member_id ?? '') == $m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Peran *</label>
            <select name="role" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                @foreach (['Kapten', 'Wakil Kapten'] as $role)
                    <option value="{{ $role }}" @selected(old('role', $captain->role ?? '') === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tim</label>
            <select name="team_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="">— JKT48 (Umum) —</option>
                @foreach ($teams as $t)
                    <option value="{{ $t->id }}" @selected(old('team_id', $captain->team_id ?? '') == $t->id)>Tim {{ $t->code }} — {{ $t->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Kosongkan untuk Kapten / Wakil Kapten JKT48 keseluruhan.</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Mulai *</label>
            <input type="date" name="start_date" required value="{{ old('start_date', $captain->start_date?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Berakhir</label>
            <input type="date" name="end_date" value="{{ old('end_date', $captain->end_date?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <p class="text-xs text-slate-500 mt-1">Kosongkan jika masih aktif.</p>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg">{{ old('notes', $captain->notes ?? '') }}</textarea>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan</button>
    <a href="{{ route('admin.captains.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
</div>
