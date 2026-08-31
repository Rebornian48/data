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

{{-- Team history section --}}
@isset($teams)
@php
    $oldHistory = old('team_history');
    if (is_array($oldHistory)) {
        $historyRows = $oldHistory;
    } else {
        $historyRows = [];
        if (isset($member) && $member->exists && $member->relationLoaded('teamHistory')) {
            foreach ($member->teamHistory as $h) {
                $historyRows[] = [
                    'team_id' => $h->team_id,
                    'joined_date' => $h->joined_date?->format('Y-m-d'),
                    'left_date' => $h->left_date?->format('Y-m-d'),
                    'notes' => $h->notes,
                ];
            }
        }
    }
@endphp
<div class="bg-white rounded-xl p-6 border border-slate-200 mt-6">
    <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h2 class="font-bold text-slate-900">Riwayat Tim</h2>
        <button type="button" onclick="addTeamHistoryRow()" class="px-3 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200">+ Baris</button>
    </div>
    <div id="team-history-rows" class="space-y-2">
        @foreach ($historyRows as $idx => $row)
            <div class="team-history-row grid grid-cols-12 gap-2 items-end border border-slate-200 rounded-lg p-3">
                <div class="col-span-3">
                    <label class="block text-xs text-slate-500 mb-1">Tim</label>
                    <select name="team_history[{{ $idx }}][team_id]" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
                        <option value="">— pilih —</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}" @selected(($row['team_id'] ?? '') == $t->id)>Tim {{ $t->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-3">
                    <label class="block text-xs text-slate-500 mb-1">Masuk</label>
                    <input type="date" name="team_history[{{ $idx }}][joined_date]" value="{{ $row['joined_date'] ?? '' }}" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
                </div>
                <div class="col-span-3">
                    <label class="block text-xs text-slate-500 mb-1">Keluar</label>
                    <input type="date" name="team_history[{{ $idx }}][left_date]" value="{{ $row['left_date'] ?? '' }}" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-slate-500 mb-1">Catatan</label>
                    <input type="text" name="team_history[{{ $idx }}][notes]" value="{{ $row['notes'] ?? '' }}" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
                </div>
                <div class="col-span-1 flex justify-end">
                    <button type="button" onclick="this.closest('.team-history-row').remove()" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">×</button>
                </div>
            </div>
        @endforeach
    </div>
    <p class="text-xs text-slate-500 mt-2">Kosongkan "Keluar" jika member masih di tim tersebut. Contoh: <em>1 Juni 2017 – Masuk Tim T</em>, lalu <em>1 Juni 2018 – Masuk Tim KIII</em>.</p>
</div>
<template id="team-history-template">
    <div class="team-history-row grid grid-cols-12 gap-2 items-end border border-slate-200 rounded-lg p-3">
        <div class="col-span-3">
            <label class="block text-xs text-slate-500 mb-1">Tim</label>
            <select name="team_history[__IDX__][team_id]" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
                <option value="">— pilih —</option>
                @foreach ($teams as $t)
                    <option value="{{ $t->id }}">Tim {{ $t->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-3">
            <label class="block text-xs text-slate-500 mb-1">Masuk</label>
            <input type="date" name="team_history[__IDX__][joined_date]" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
        </div>
        <div class="col-span-3">
            <label class="block text-xs text-slate-500 mb-1">Keluar</label>
            <input type="date" name="team_history[__IDX__][left_date]" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
        </div>
        <div class="col-span-2">
            <label class="block text-xs text-slate-500 mb-1">Catatan</label>
            <input type="text" name="team_history[__IDX__][notes]" class="w-full text-sm px-2 py-1.5 border border-slate-300 rounded">
        </div>
        <div class="col-span-1 flex justify-end">
            <button type="button" onclick="this.closest('.team-history-row').remove()" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">×</button>
        </div>
    </div>
</template>
<script>
    (function () {
        window.__teamHistoryIdx = {{ count($historyRows) }};
    })();
    function addTeamHistoryRow() {
        const tpl = document.getElementById('team-history-template').innerHTML;
        const idx = window.__teamHistoryIdx++;
        const html = tpl.replace(/__IDX__/g, idx);
        document.getElementById('team-history-rows').insertAdjacentHTML('beforeend', html);
    }
</script>
@endisset

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
