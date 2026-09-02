@extends('layouts.admin')

@section('title', 'Dokumentasi API')
@section('page_title', 'Dokumentasi API')
@section('page_subtitle', 'Referensi REST API JKT48 Data (OpenAPI 3.0)')

@section('content')
<div class="grid gap-6 md:grid-cols-3 mb-6">
    <div class="p-5" style="background:#ff6b9d;border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Base URL</div>
        <div class="font-mono text-sm break-all text-black">{{ $baseUrl }}</div>
    </div>
    <div class="p-5" style="background:#ffd23f;border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Swagger UI</div>
        <a href="{{ $docsUrl }}" target="_blank" rel="noopener" class="font-mono text-sm break-all text-black underline">
            {{ $docsUrl }}
        </a>
    </div>
    <div class="p-5" style="background:#a3e635;border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">OpenAPI Spec</div>
        <a href="{{ $specUrl }}" target="_blank" rel="noopener" class="font-mono text-sm break-all text-black underline">
            {{ $specUrl }}
        </a>
    </div>
</div>

<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Ringkasan Endpoint</h2>
    <p class="text-sm text-black mb-4">
        Semua endpoint read-only, tanpa autentikasi (public), berespons JSON.
        Support pagination (<code>?page</code>, <code>?per_page</code>) dan filter query string.
    </p>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="border:2px solid #000;">
            <thead>
                <tr style="background:#fce7f3;">
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Method</th>
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Path</th>
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Deskripsi</th>
                </tr>
            </thead>
            <tbody class="font-mono">
                @php
                    $rows = [
                        ['GET', '/v1/members',                 'List member (filter: status, generation_id, search, sort, per_page)'],
                        ['GET', '/v1/members/{id}',            'Detail member + generasi, tim aktif, single'],
                        ['GET', '/v1/singles',                 'List single (filter: search, sort)'],
                        ['GET', '/v1/singles/{id}',            'Detail single + daftar senbatsu'],
                        ['GET', '/v1/generations',             'List generasi + counter member'],
                        ['GET', '/v1/generations/{id}',        'Detail generasi + daftar member'],
                        ['GET', '/v1/teams',                   'List tim (filter: active_only)'],
                        ['GET', '/v1/teams/{id}',              'Detail tim + current member + kapten'],
                        ['GET', '/v1/captains',                'List kapten (filter: active_only, team_id, role)'],
                        ['GET', '/v1/statistics',              'Statistik total & breakdown per generasi'],
                    ];
                @endphp
                @foreach ($rows as $r)
                    <tr>
                        <td class="px-3 py-2" style="border:2px solid #000;background:#dbeafe;color:#1e3a8a;font-weight:700;">{{ $r[0] }}</td>
                        <td class="px-3 py-2" style="border:2px solid #000;">{{ $r[1] }}</td>
                        <td class="px-3 py-2" style="border:2px solid #000;font-family:inherit;">{{ $r[2] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Contoh Request</h2>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <div class="text-xs font-bold uppercase mb-1">cURL</div>
            <pre class="text-xs p-3 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">curl "{{ $baseUrl }}/v1/members?status=Aktif&per_page=10"</pre>
        </div>
        <div>
            <div class="text-xs font-bold uppercase mb-1">JavaScript (fetch)</div>
<pre class="text-xs p-3 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">const res = await fetch('{{ $baseUrl }}/v1/statistics');
const { data } = await res.json();
console.log(data.totals);</pre>
        </div>
    </div>
</div>

<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Swagger UI (embedded)</h2>
    <p class="text-sm text-black mb-3">
        Jelajahi interaktif — coba semua endpoint langsung dari sini.
        Kalau lebih nyaman fullscreen, buka <a href="{{ $docsUrl }}" target="_blank" class="underline">{{ $docsUrl }}</a>.
    </p>
    <div style="border:3px solid #000;box-shadow:5px 5px 0 #000;background:#fff;">
        <iframe src="{{ $docsUrl }}"
                style="width:100%;height:750px;border:0;display:block;background:#fff;"
                title="Swagger UI"></iframe>
    </div>
</div>

<div class="p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Response Format</h2>
    <div class="text-sm text-black space-y-2">
        <p>
            <strong>Endpoint list</strong> → paginated envelope:
        </p>
<pre class="text-xs p-3 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">{
  "data": [ {...}, {...} ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta":  { "current_page": 1, "per_page": 25, "last_page": 8, "total": 200 }
}</pre>
        <p><strong>Endpoint detail</strong> → <code>{ "data": {...} }</code></p>
        <p><strong>Validasi gagal</strong> → HTTP 422 dengan <code>{ "message", "errors": {field: [..]} }</code></p>
        <p><strong>Not found</strong> → HTTP 404 dengan <code>{ "message" }</code></p>
    </div>
</div>
@endsection
