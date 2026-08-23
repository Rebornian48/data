@extends('layouts.app')

@section('title', $sorterTitle . ' - JKT48 Database')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">
            <a href="{{ route('sorter.index') }}" class="hover:text-brand">Sorter</a>
            <span class="mx-1">/</span>
            <span>Member</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-1">{{ $sorterTitle }}</h1>
        <p class="text-slate-500 dark:text-slate-400">{{ $sorterSubtitle }}</p>
    </div>

    {{-- ============ STAGE: FILTER ============ --}}
    <section id="stage-filter" class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <h2 class="font-semibold text-slate-900 dark:text-slate-100 mb-4">Pilih member yang ikut di-sort</h2>

        <div class="mb-5">
            <div class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</div>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                    <input type="checkbox" id="filter-status-aktif" class="filter-status w-4 h-4 accent-red-600" value="Aktif" checked>
                    Aktif
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                    <input type="checkbox" id="filter-status-lulus" class="filter-status w-4 h-4 accent-red-600" value="Lulus">
                    Lulus
                </label>
            </div>
        </div>

        <div class="mb-5">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-slate-700 dark:text-slate-300">Generasi</div>
                <div class="flex gap-2 text-xs">
                    <button type="button" id="gen-all" class="text-brand hover:underline">Pilih semua</button>
                    <span class="text-slate-300 dark:text-slate-600">|</span>
                    <button type="button" id="gen-none" class="text-slate-600 dark:text-slate-400 hover:underline">Kosongkan</button>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @foreach ($generations as $gen)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 hover:border-brand">
                        <input type="checkbox" class="filter-gen w-4 h-4 accent-red-600" value="{{ $gen->id }}" checked>
                        <span class="truncate">{{ $gen->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-4">
            <div class="text-sm text-slate-600 dark:text-slate-400">
                <span id="filter-count" class="font-semibold text-slate-900 dark:text-slate-100">0</span> member terpilih
                <span id="filter-hint" class="text-xs text-slate-400 ml-2">(minimal 2)</span>
            </div>
            <button type="button" id="btn-start"
                    class="bg-brand text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                Mulai Sorter
            </button>
        </div>
    </section>

    {{-- ============ STAGE: SORTING ============ --}}
    <section id="stage-sort" class="hidden">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-slate-600 dark:text-slate-400">
                    Perbandingan #<span id="battle-num">1</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-undo" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed" disabled>Undo</button>
                    <button type="button" id="btn-restart" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Restart</button>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <span>Progress</span>
                    <span><span id="progress-pct">0</span>%</span>
                </div>
                <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div id="progress-bar" class="h-full gradient-brand transition-all" style="width: 0%"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4 items-stretch">
                <button type="button" id="card-left" class="group flex flex-col items-center p-5 bg-slate-50 dark:bg-slate-700/40 border-2 border-slate-200 dark:border-slate-700 rounded-xl hover:border-brand hover:shadow-lg transition">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 mb-3">
                        <img id="img-left" alt="" class="w-full h-full object-cover hidden">
                        <div id="ph-left" class="w-full h-full flex items-center justify-center text-3xl font-bold text-white gradient-brand"></div>
                    </div>
                    <div id="name-left" class="font-semibold text-slate-900 dark:text-slate-100 text-center"></div>
                    <div id="sub-left" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"></div>
                </button>

                <div class="flex md:flex-col items-center justify-center gap-3">
                    <button type="button" id="card-tie" class="px-5 py-2.5 rounded-lg border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:border-brand hover:text-brand transition">
                        Seri
                    </button>
                    <div class="text-xs text-slate-400 hidden md:block">VS</div>
                </div>

                <button type="button" id="card-right" class="group flex flex-col items-center p-5 bg-slate-50 dark:bg-slate-700/40 border-2 border-slate-200 dark:border-slate-700 rounded-xl hover:border-brand hover:shadow-lg transition">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 mb-3">
                        <img id="img-right" alt="" class="w-full h-full object-cover hidden">
                        <div id="ph-right" class="w-full h-full flex items-center justify-center text-3xl font-bold text-white gradient-brand"></div>
                    </div>
                    <div id="name-right" class="font-semibold text-slate-900 dark:text-slate-100 text-center"></div>
                    <div id="sub-right" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"></div>
                </button>
            </div>

            <div class="mt-4 text-center text-xs text-slate-500 dark:text-slate-400">
                Tip: gunakan tombol <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded border border-slate-300 dark:border-slate-600 text-xs">←</kbd>
                / <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded border border-slate-300 dark:border-slate-600 text-xs">→</kbd>
                / <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded border border-slate-300 dark:border-slate-600 text-xs">↓</kbd> (seri)
                atau <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded border border-slate-300 dark:border-slate-600 text-xs">U</kbd> (undo).
            </div>
        </div>
    </section>

    {{-- ============ STAGE: RESULT ============ --}}
    <section id="stage-result" class="hidden">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Hasil Peringkat</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Berdasarkan pilihan yang kamu buat.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex rounded-lg border border-slate-300 dark:border-slate-600 overflow-hidden text-xs">
                        <button type="button" data-rank="unique"  class="rank-toggle px-3 py-1.5 font-medium bg-brand text-white">Unik</button>
                        <button type="button" data-rank="seq"     class="rank-toggle px-3 py-1.5 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Tie 1,1,2,3</button>
                        <button type="button" data-rank="skip"    class="rank-toggle px-3 py-1.5 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Tie 1,1,3,4</button>
                    </div>
                    <button type="button" id="btn-copy" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Salin Teks</button>
                    <button type="button" id="btn-shot" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Screenshot</button>
                    <button type="button" id="btn-again" class="text-xs px-3 py-1.5 rounded-lg bg-brand text-white hover:bg-red-700">Sort Lagi</button>
                </div>
            </div>

            <div id="result-list" class="space-y-2"></div>
        </div>
    </section>

</div>

<script>
    window.SORTER_ITEMS = @json($items);
</script>
<script src="{{ asset('js/sorter-member.js') }}?v=1"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
@endsection
