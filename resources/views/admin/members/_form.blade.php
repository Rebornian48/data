@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <div class="font-semibold mb-1">Ada kesalahan pada form:</div>
        <ul class="text-sm list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Left column: identity --}}
    <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4">
        <h2 class="font-bold text-slate-900 border-b pb-2">Identitas</h2>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Panggilan</label>
            <input type="text" name="nickname" value="{{ old('nickname', $member->nickname ?? '') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Generasi <span class="text-red-500">*</span></label>
            <select name="generation_id" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                @foreach ($generations as $gen)
                    <option value="{{ $gen->id }}" @selected(old('generation_id', $member->generation_id ?? '') == $gen->id)>
                        {{ $gen->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir</label>
                <input type="text" name="birth_place" value="{{ old('birth_place', $member->birth_place ?? '') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d') ?? '') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Foto (URL)</label>
            <input type="text" name="photo_url" value="{{ old('photo_url', $member->photo_url ?? '') }}"
                   placeholder="https://... atau /public/img/nama.jpg"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Bio</label>
            <textarea name="bio" rows="4"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('bio', $member->bio ?? '') }}</textarea>
        </div>
    </div>

    {{-- Right column: career --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4">
            <h2 class="font-bold text-slate-900 border-b pb-2">Karier</h2>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="Aktif" @selected(old('status', $member->status ?? 'Aktif') === 'Aktif')>Aktif</option>
                    <option value="Lulus" @selected(old('status', $member->status ?? '') === 'Lulus')>Lulus</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Masuk</label>
                    <input type="date" name="join_date" value="{{ old('join_date', $member->join_date?->format('Y-m-d') ?? '') }}"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Promosi</label>
                    <input type="date" name="promotion_date" value="{{ old('promotion_date', $member->promotion_date?->format('Y-m-d') ?? '') }}"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Dibatalkan</label>
                    <input type="date" name="cancelled_date" value="{{ old('cancelled_date', $member->cancelled_date?->format('Y-m-d') ?? '') }}"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Masuk Kembali</label>
                    <input type="date" name="rejoin_date" value="{{ old('rejoin_date', $member->rejoin_date?->format('Y-m-d') ?? '') }}"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4">
            <h2 class="font-bold text-slate-900 border-b pb-2">Kelulusan</h2>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Umumkan Lulus</label>
                <input type="date" name="graduation_announce_date" value="{{ old('graduation_announce_date', $member->graduation_announce_date?->format('Y-m-d') ?? '') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Event Pengumuman</label>
                <input type="text" name="graduation_announce_event" value="{{ old('graduation_announce_event', $member->graduation_announce_event ?? '') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tgl. Lulus</label>
                <input type="date" name="graduation_date" value="{{ old('graduation_date', $member->graduation_date?->format('Y-m-d') ?? '') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>
        </div>
    </div>
</div>

{{-- Singles section --}}
@isset($singles)
<div class="bg-white rounded-xl p-6 border border-slate-200 mt-6">
    <h2 class="font-bold text-slate-900 border-b pb-2 mb-4">Partisipasi Single</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @php
            $memberSingles = isset($member) ? $member->singles->keyBy('id')->map(fn($s) => $s->pivot->role) : collect();
        @endphp
        @foreach ($singles as $single)
            <div class="border border-slate-200 rounded-lg p-2">
                <div class="text-xs font-bold text-slate-900">{{ $single->code }}</div>
                <div class="text-xs text-slate-500 truncate mb-2" title="{{ $single->title }}">{{ $single->title }}</div>
                <select name="singles[{{ $single->id }}]" class="w-full text-xs px-1 py-1 border border-slate-300 rounded">
                    <option value="">-</option>
                    <option value="member" @selected(old("singles.{$single->id}", $memberSingles[$single->id] ?? '') === 'member')>Member</option>
                    <option value="center" @selected(old("singles.{$single->id}", $memberSingles[$single->id] ?? '') === 'center')>Center</option>
                </select>
            </div>
        @endforeach
    </div>
</div>
@endisset

<div class="mt-6 flex gap-3">
    <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">
        Simpan
    </button>
    <a href="{{ route('admin.members.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">
        Batal
    </a>
</div>
