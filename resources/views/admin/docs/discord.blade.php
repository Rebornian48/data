@extends('layouts.admin')

@section('title', 'Dokumentasi Discord Bot')
@section('page_title', 'Discord Bot')
@section('page_subtitle', 'Setup, konfigurasi, dan daftar perintah bot Discord JKT48 Data')

@section('content')
{{-- Status --}}
<div class="grid gap-4 md:grid-cols-4 mb-6">
    <div class="p-4" style="background:{{ $enabled ? '#a3e635' : '#e5e7eb' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Status</div>
        <div class="text-lg font-black">{{ $enabled ? 'AKTIF' : 'NONAKTIF' }}</div>
        <div class="text-[10px] font-bold mt-1">DISCORD_ENABLED</div>
    </div>
    <div class="p-4" style="background:{{ $webhookCount ? '#a3e635' : '#fef3c7' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Webhook Broadcast</div>
        <div class="text-lg font-black">{{ $webhookCount }} channel</div>
        <div class="text-[10px] font-bold mt-1">DISCORD_WEBHOOK_URLS</div>
    </div>
    <div class="p-4" style="background:{{ $hasApplicationId ? '#a3e635' : '#fecaca' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Application ID</div>
        <div class="text-lg font-black">{{ $hasApplicationId ? 'TERSET' : 'KOSONG' }}</div>
        <div class="text-[10px] font-bold mt-1">DISCORD_APPLICATION_ID</div>
    </div>
    <div class="p-4" style="background:{{ $hasPublicKey ? '#a3e635' : '#fecaca' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Public Key</div>
        <div class="text-lg font-black">{{ $hasPublicKey ? 'TERSET' : 'KOSONG' }}</div>
        <div class="text-[10px] font-bold mt-1">DISCORD_PUBLIC_KEY</div>
    </div>
</div>

{{-- Overview --}}
<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-2">Dua Mode Integrasi</h2>
    <div class="grid gap-3 md:grid-cols-2 text-sm text-black">
        <div class="p-3" style="background:#fce7f3;border:2px solid #000;">
            <div class="font-bold mb-1">1. Incoming Webhook (paling gampang)</div>
            <div>Untuk <em>broadcast harian saja</em> (kirim pesan ke channel). Cukup satu URL webhook per channel. Tidak butuh Application ID / Public Key / Bot Token.</div>
        </div>
        <div class="p-3" style="background:#fef3c7;border:2px solid #000;">
            <div class="font-bold mb-1">2. Slash Command (opsional)</div>
            <div>Supaya user bisa <code>/ultah</code>, <code>/lulus</code>, dst. langsung dari Discord. Butuh app di Discord Developer Portal (Application ID + Public Key + Bot Token) &amp; endpoint interactions publik.</div>
        </div>
    </div>
</div>

{{-- Setup A --}}
<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">A. Setup Broadcast (Incoming Webhook)</h2>
    <ol class="text-sm text-black space-y-3 list-decimal list-inside">
        <li>Di server Discord, buka <strong>Channel Settings → Integrations → Webhooks → New Webhook</strong>. Beri nama (mis. <code>JKT48 Bot</code>), copy webhook URL.</li>
        <li>
            Isi <code>.env</code>:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">DISCORD_ENABLED=true
DISCORD_WEBHOOK_URLS=https://discord.com/api/webhooks/xxxx/yyyy</pre>
            Boleh multiple webhook (pisah koma) untuk broadcast ke beberapa channel/server sekaligus.
        </li>
        <li>Pastikan scheduler jalan di server:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">* * * * * cd /path/to/app && php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</pre>
            Broadcast otomatis tiap hari jam <code>{{ $dailyTime }}</code> WIB. Uji manual: <code>php artisan notifications:daily</code>.</li>
    </ol>
</div>

{{-- Setup B --}}
<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">B. Setup Slash Command (Interactions)</h2>
    <ol class="text-sm text-black space-y-3 list-decimal list-inside">
        <li>
            Buat app di
            <a class="underline" href="https://discord.com/developers/applications" target="_blank" rel="noopener">Discord Developer Portal</a>.
            Salin <strong>Application ID</strong> &amp; <strong>Public Key</strong> di tab <em>General Information</em>.
        </li>
        <li>
            Tab <strong>Bot → Reset Token</strong>, salin <strong>Bot Token</strong>.
        </li>
        <li>
            Isi <code>.env</code>:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">DISCORD_APPLICATION_ID=1234567890
DISCORD_PUBLIC_KEY=abcdef012345...
DISCORD_BOT_TOKEN=Bot xxxxxxx</pre>
        </li>
        <li>
            Set <strong>Interactions Endpoint URL</strong> pada tab <em>General Information</em> ke:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">{{ $interactionsUrl }}</pre>
            Discord akan mengirim <code>PING</code> untuk verifikasi — kalau Public Key benar &amp; app running, tersimpan.
        </li>
        <li>
            <strong>Daftarkan slash command</strong> (global — muncul di semua server dalam &lt;1 jam):
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">curl -X PUT \
  -H "Authorization: Bot $DISCORD_BOT_TOKEN" \
  -H "Content-Type: application/json" \
  "https://discord.com/api/v10/applications/$DISCORD_APPLICATION_ID/commands" \
  -d '[
    {"name":"ultah",  "description":"Ulang tahun hari ini"},
    {"name":"lulus",  "description":"Kelulusan bulan ini"},
    {"name":"jadwal", "description":"Event bulan ini"},
    {"name":"help",   "description":"Bantuan"},
    {"name":"member", "description":"Cari member",
     "options":[{"name":"nama","description":"Nama / nickname","type":3,"required":true}]}
  ]'</pre>
            Untuk testing cepat (langsung muncul di 1 guild saja), tambahkan <code>/guilds/&lt;GUILD_ID&gt;</code> sebelum <code>/commands</code>.
        </li>
        <li>
            <strong>Invite bot ke server:</strong>
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">https://discord.com/oauth2/authorize?client_id=&lt;APPLICATION_ID&gt;&amp;scope=applications.commands+bot&amp;permissions=2048</pre>
        </li>
    </ol>
</div>

{{-- Commands --}}
<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Daftar Perintah</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="border:2px solid #000;">
            <thead>
                <tr style="background:#fce7f3;">
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Perintah</th>
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Opsi</th>
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $commands = [
                        ['/ultah',  '—',                    'Daftar member yang ulang tahun HARI INI (status Aktif).'],
                        ['/lulus',  '—',                    'Daftar kelulusan bulan berjalan.'],
                        ['/member', 'nama (string, wajib)', 'Cari member berdasarkan nama/nickname/tempat lahir (max 5 hasil).'],
                        ['/jadwal', '—',                    'Event bulan berjalan (ulang tahun, kelulusan, anniversary generasi).'],
                        ['/help',   '—',                    'Menampilkan bantuan.'],
                    ];
                @endphp
                @foreach ($commands as $c)
                    <tr>
                        <td class="px-3 py-2 font-mono font-bold" style="border:2px solid #000;background:#dbeafe;">{{ $c[0] }}</td>
                        <td class="px-3 py-2 font-mono" style="border:2px solid #000;">{{ $c[1] }}</td>
                        <td class="px-3 py-2" style="border:2px solid #000;">{{ $c[2] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Troubleshooting --}}
<div class="p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Troubleshooting</h2>
    <div class="space-y-3 text-sm text-black">
        <div>
            <div class="font-bold">❌ Discord: "Interactions endpoint validation failed".</div>
            <div>Public Key salah, atau endpoint bukan HTTPS/tidak reachable, atau library <code>sodium</code> di PHP nonaktif (butuh untuk verifikasi Ed25519). Cek <code>php -m | grep sodium</code>.</div>
        </div>
        <div>
            <div class="font-bold">❌ Slash command tidak muncul.</div>
            <div>Command global butuh sampai 1 jam untuk propagasi. Untuk instan, daftarkan sebagai guild command (endpoint <code>/applications/&lt;APP_ID&gt;/guilds/&lt;GUILD_ID&gt;/commands</code>) — muncul langsung.</div>
        </div>
        <div>
            <div class="font-bold">❌ Broadcast tidak masuk channel.</div>
            <div>Cek <code>DISCORD_ENABLED=true</code> dan <code>DISCORD_WEBHOOK_URLS</code> valid (URL webhook, bukan link channel). Uji: <code>php artisan notifications:daily</code>.</div>
        </div>
        <div>
            <div class="font-bold">❌ 401 pada endpoint interactions.</div>
            <div>Signature Ed25519 mismatch — biasanya Public Key salah copy. Salin ulang dari Developer Portal &amp; restart worker/php-fpm supaya <code>.env</code> ke-reload.</div>
        </div>
    </div>
</div>
@endsection
