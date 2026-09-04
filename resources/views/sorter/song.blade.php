@extends('layouts.app')

@section('title', $sorterTitle . ' - JKT48 Database')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
    .neo {
        --ground: #fef2d0; --ink: #000;
        --yellow: #ffd23f; --pink: #ff6b9d; --lime: #a3e635; --cyan: #22d3ee; --red: #ef4444;
        font-family: 'Space Grotesk', 'Inter', sans-serif;
        background: var(--ground); color: var(--ink);
        min-height: calc(100vh - 4rem);
    }
    .neo h1, .neo h2, .neo .display { font-family: 'Archivo Black', 'Space Grotesk', sans-serif; letter-spacing: -0.02em; text-transform: uppercase; }
    .neo-card { border: 3px solid var(--ink); box-shadow: 6px 6px 0 var(--ink); background: #fff; }
    .neo-chip { border: 3px solid var(--ink); background: #fff; font-weight: 700; }
    .neo-btn {
        border: 3px solid var(--ink); box-shadow: 4px 4px 0 var(--ink);
        font-weight: 700; color: #000; background: #fff;
        transition: transform .08s ease, box-shadow .08s ease;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .neo-btn:hover { transform: translate(-1px,-1px); box-shadow: 5px 5px 0 var(--ink); }
    .neo-btn:active, .neo-btn.is-pressed { transform: translate(4px,4px); box-shadow: 0 0 0 var(--ink); }
    .neo-btn:focus-visible { outline: 3px dashed var(--cyan); outline-offset: 4px; }
    .neo-btn:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: 4px 4px 0 var(--ink); }
    .neo-btn:disabled:hover { transform: none; box-shadow: 4px 4px 0 var(--ink); }

    .neo-btn-yellow { background: var(--yellow); }
    .neo-btn-pink { background: var(--pink); }
    .neo-btn-lime { background: var(--lime); }
    .neo-btn-red { background: var(--red); color: #000; }
    .neo-btn-black { background: #000; color: #fff; }

    .neo-tile {
        border: 3px solid var(--ink); box-shadow: 6px 6px 0 var(--ink);
        background: #fff; transition: transform .08s ease, box-shadow .08s ease;
        cursor: pointer;
    }
    .neo-tile:hover { transform: translate(-1px,-1px); box-shadow: 7px 7px 0 var(--ink); }
    .neo-tile:active, .neo-tile.is-pressed { transform: translate(6px,6px); box-shadow: 0 0 0 var(--ink); }
    .neo-tile:focus-visible { outline: 3px dashed var(--cyan); outline-offset: 4px; }

    .neo-avatar { border: 3px solid var(--ink); background: var(--yellow); }
    .neo-input { border: 3px solid var(--ink); background: #fff; font-weight: 600; color: #000; padding: .6rem .8rem; }
    .neo-input:focus-visible { outline: 3px dashed var(--cyan); outline-offset: 3px; }

    .neo-check { appearance: none; width: 22px; height: 22px; border: 3px solid var(--ink); background: #fff; display: inline-grid; place-content: center; cursor: pointer; flex-shrink: 0; }
    .neo-check:checked { background: var(--pink); }
    .neo-check:checked::before { content: ''; width: 10px; height: 10px; background: #000; }
    .neo-check:focus-visible { outline: 3px dashed var(--cyan); outline-offset: 3px; }

    .neo-progress-outer { border: 3px solid var(--ink); background: #fff; height: 22px; padding: 3px; }
    .neo-progress-inner { background: var(--lime); height: 100%; transition: width .2s ease; }

    .neo-kbd { border: 3px solid var(--ink); background: #fff; padding: 2px 8px; font-weight: 700; font-family: 'Space Grotesk', monospace; font-size: 11px; box-shadow: 2px 2px 0 var(--ink); }

    .neo-rank-toggle { border: 3px solid var(--ink); font-weight: 700; background: #fff; padding: .4rem .8rem; }
    .neo-rank-toggle.is-active { background: var(--pink); color: #000; }
    .neo-rank-toggle:focus-visible { outline: 3px dashed var(--cyan); outline-offset: 3px; }

    .neo-badge-aktif { background: var(--lime); border: 3px solid var(--ink); font-weight: 700; padding: 2px 8px; color: #000; }
    .neo-badge-lulus { background: #fff; border: 3px solid var(--ink); font-weight: 700; padding: 2px 8px; color: #000; }

    @media (prefers-reduced-motion: reduce) {
        .neo-tile, .neo-tile:hover, .neo-tile:active, .neo-tile.is-pressed,
        .neo-btn, .neo-btn:hover, .neo-btn:active, .neo-btn.is-pressed,
        .neo-progress-inner {
            transition: none; transform: none;
        }
        .neo-tile { box-shadow: 6px 6px 0 var(--ink); }
        .neo-btn { box-shadow: 4px 4px 0 var(--ink); }
    }
</style>

<div class="neo">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <div class="text-xs font-bold mb-2">
            <a href="{{ route('sorter.index') }}" class="underline">SORTER</a>
            <span class="mx-1">/</span>
            <span>SONG</span>
        </div>
        <h1 class="text-4xl sm:text-5xl mb-2">{{ $sorterTitle }}</h1>
        <p class="font-medium">{{ $sorterSubtitle }}</p>
    </div>

    {{-- ============ STAGE: FILTER ============ --}}
    <section id="stage-filter" class="neo-card p-6">
        <h2 class="text-xl mb-5">PILIH LAGU YANG IKUT DI-SORT</h2>

        <div class="mb-5">
            <div class="display text-sm mb-3">STATUS RILIS</div>
            <div class="flex flex-wrap gap-3">
                <label class="inline-flex items-center gap-2 cursor-pointer neo-chip px-3 py-2">
                    <input type="checkbox" class="filter-released neo-check" value="released" checked>
                    <span class="font-bold">SUDAH RILIS</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer neo-chip px-3 py-2">
                    <input type="checkbox" class="filter-released neo-check" value="unreleased">
                    <span class="font-bold">BELUM RILIS</span>
                </label>
            </div>
        </div>

        <div class="mb-5">
            <div class="flex items-center justify-between mb-3">
                <div class="display text-sm">ASAL GRUP</div>
                <div class="flex gap-2">
                    <button type="button" id="origin-all" class="neo-btn neo-btn-lime px-3 py-1 text-xs">PILIH SEMUA</button>
                    <button type="button" id="origin-none" class="neo-btn px-3 py-1 text-xs">KOSONGKAN</button>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($originGroups as $og)
                    <label class="inline-flex items-center gap-2 cursor-pointer neo-chip px-3 py-2">
                        <input type="checkbox" class="filter-origin neo-check" value="{{ $og }}" checked>
                        <span class="truncate font-bold">{{ $og }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mb-5">
            <div class="display text-sm mb-3">CARI JUDUL</div>
            <input type="text" id="filter-search" class="neo-input w-full" placeholder="Cari lagu (contoh: Heavy Rotation)">
        </div>

        <div class="flex items-center justify-between border-t-[3px] border-black pt-4">
            <div class="font-bold">
                <span id="filter-count" class="display text-2xl">0</span> LAGU TERPILIH
                <span id="filter-hint" class="text-xs font-medium ml-2">(minimal 2)</span>
            </div>
            <button type="button" id="btn-start" class="neo-btn neo-btn-pink px-6 py-3 text-sm" disabled>
                MULAI SORTER
            </button>
        </div>
    </section>

    {{-- ============ STAGE: SORTING ============ --}}
    <section id="stage-sort" class="hidden">
        <div class="neo-card p-6">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div class="display text-sm">
                    PERBANDINGAN #<span id="battle-num">1</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-undo" class="neo-btn px-3 py-1.5 text-xs" disabled>UNDO</button>
                    <button type="button" id="btn-restart" class="neo-btn neo-btn-red px-3 py-1.5 text-xs">RESTART</button>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between text-xs font-bold mb-2">
                    <span>PROGRESS</span>
                    <span><span id="progress-pct">0</span>%</span>
                </div>
                <div class="neo-progress-outer">
                    <div id="progress-bar" class="neo-progress-inner" style="width: 0%"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-6 items-stretch">
                <button type="button" id="card-left" class="neo-tile p-5 flex flex-col items-center">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 overflow-hidden neo-avatar mb-3">
                        <img id="img-left" alt="" class="w-full h-full object-cover hidden">
                        <div id="ph-left" class="w-full h-full flex items-center justify-center text-4xl display text-black"></div>
                    </div>
                    <div id="name-left" class="display text-lg text-center"></div>
                    <div id="sub-left" class="text-xs font-bold mt-1 text-center"></div>
                </button>

                <div class="flex md:flex-col items-center justify-center gap-3">
                    <button type="button" id="card-tie" class="neo-btn neo-btn-yellow px-5 py-2.5 text-sm">SERI</button>
                    <div class="display text-xs hidden md:block">VS</div>
                </div>

                <button type="button" id="card-right" class="neo-tile p-5 flex flex-col items-center">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 overflow-hidden neo-avatar mb-3">
                        <img id="img-right" alt="" class="w-full h-full object-cover hidden">
                        <div id="ph-right" class="w-full h-full flex items-center justify-center text-4xl display text-black"></div>
                    </div>
                    <div id="name-right" class="display text-lg text-center"></div>
                    <div id="sub-right" class="text-xs font-bold mt-1 text-center"></div>
                </button>
            </div>

            <div class="mt-5 text-center text-xs font-bold">
                Tip: <kbd class="neo-kbd">←</kbd> <kbd class="neo-kbd">→</kbd> <kbd class="neo-kbd">↓</kbd> (seri) <kbd class="neo-kbd">U</kbd> (undo)
            </div>
        </div>
    </section>

    {{-- ============ STAGE: RESULT ============ --}}
    <section id="stage-result" class="hidden">
        <div class="neo-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-2xl">HASIL PERINGKAT</h2>
                    <p class="text-xs font-bold mt-1">Berdasarkan pilihan yang kamu buat.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex text-xs">
                        <button type="button" data-rank="unique" class="rank-toggle neo-rank-toggle is-active">UNIK</button>
                        <button type="button" data-rank="seq"    class="rank-toggle neo-rank-toggle" style="border-left: 0;">TIE 1,1,2,3</button>
                        <button type="button" data-rank="skip"   class="rank-toggle neo-rank-toggle" style="border-left: 0;">TIE 1,1,3,4</button>
                    </div>
                    <button type="button" id="btn-copy" class="neo-btn neo-btn-lime px-3 py-1.5 text-xs">SALIN TEKS</button>
                    <button type="button" id="btn-shot" class="neo-btn neo-btn-yellow px-3 py-1.5 text-xs">SCREENSHOT</button>
                    <button type="button" id="btn-again" class="neo-btn neo-btn-pink px-3 py-1.5 text-xs">SORT LAGI</button>
                </div>
            </div>

            <div id="result-list" class="space-y-3"></div>
        </div>
    </section>

</div>
</div>

<script>
    window.SORTER_ITEMS = @json($items);
</script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
<script src="{{ asset('js/sorter-core.js') }}?v=1"></script>
<script src="{{ asset('js/sorter-song.js') }}?v=1"></script>
@endsection
