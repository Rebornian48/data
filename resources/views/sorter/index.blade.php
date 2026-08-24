@extends('layouts.app')

@section('title', 'Sorter - JKT48 Database')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
    .neo { --ground: #fef2d0; --ink: #000; --yellow: #ffd23f; --pink: #ff6b9d; --lime: #a3e635; --cyan: #22d3ee; font-family: 'Space Grotesk', 'Inter', sans-serif; background: var(--ground); color: var(--ink); min-height: calc(100vh - 4rem); }
    .neo h1, .neo h2, .neo .display { font-family: 'Archivo Black', 'Space Grotesk', sans-serif; letter-spacing: -0.02em; }
    .neo-card { border: 3px solid var(--ink); box-shadow: 6px 6px 0 var(--ink); background: #fff; }
    .neo-tile { border: 3px solid var(--ink); box-shadow: 6px 6px 0 var(--ink); transition: transform .08s ease, box-shadow .08s ease; }
    .neo-tile:hover { transform: translate(-1px,-1px); box-shadow: 7px 7px 0 var(--ink); }
    .neo-tile:active, .neo-tile.is-pressed { transform: translate(6px,6px); box-shadow: 0 0 0 var(--ink); }
    .neo-tile:focus-visible { outline: 3px dashed var(--cyan); outline-offset: 4px; }
    .bg-neo-yellow { background: var(--yellow); }
    .bg-neo-pink { background: var(--pink); }
    .bg-neo-lime { background: var(--lime); }
    .bg-neo-white { background: #fff; }
    @media (prefers-reduced-motion: reduce) {
        .neo-tile, .neo-tile:hover, .neo-tile:active, .neo-tile.is-pressed { transition: none; transform: none; box-shadow: 6px 6px 0 var(--ink); }
    }
</style>

<div class="neo">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-4xl sm:text-5xl font-black text-black mb-2">JKT48 SORTER</h1>
        <p class="text-black font-medium">Urutkan berbagai koleksi JKT48 lewat perbandingan berpasangan.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <a href="{{ route('sorter.show', 'member') }}" class="neo-tile bg-neo-yellow p-6 block">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-black text-white flex items-center justify-center text-2xl display shrink-0" style="border: 3px solid #000;">M</div>
                <div>
                    <div class="display text-xl text-black">MEMBER SORTER</div>
                    <div class="text-sm text-black mt-1 font-medium">Urutkan member JKT48 favoritmu. Filter per generasi &amp; status.</div>
                </div>
            </div>
        </a>

        <div class="neo-tile bg-neo-white p-6" style="opacity: .75;">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-black text-white flex items-center justify-center text-2xl display shrink-0" style="border: 3px solid #000;">?</div>
                <div>
                    <div class="display text-xl text-black">SEGERA HADIR</div>
                    <div class="text-sm text-black mt-1 font-medium">Sorter lagu, setlist, dan koleksi lain akan menyusul.</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
