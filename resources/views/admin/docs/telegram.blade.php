@extends('layouts.admin')

@section('title', 'Dokumentasi Telegram Bot')
@section('page_title', 'Telegram Bot')
@section('page_subtitle', 'Setup, konfigurasi, dan daftar perintah bot Telegram JKT48 Data')

@section('content')
{{-- Status --}}
<div class="grid gap-4 md:grid-cols-4 mb-6">
    <div class="p-4" style="background:{{ $enabled ? '#a3e635' : '#e5e7eb' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Status</div>
        <div class="text-lg font-black">{{ $enabled ? 'AKTIF' : 'NONAKTIF' }}</div>
        <div class="text-[10px] font-bold mt-1">TELEGRAM_ENABLED</div>
    </div>
    <div class="p-4" style="background:{{ $hasBotToken ? '#a3e635' : '#fecaca' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Bot Token</div>
        <div class="text-lg font-black">{{ $hasBotToken ? 'TERSET' : 'KOSONG' }}</div>
        <div class="text-[10px] font-bold mt-1">TELEGRAM_BOT_TOKEN</div>
    </div>
    <div class="p-4" style="background:{{ $hasSecret ? '#a3e635' : '#fecaca' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Webhook Secret</div>
        <div class="text-lg font-black">{{ $hasSecret ? 'TERSET' : 'KOSONG' }}</div>
        <div class="text-[10px] font-bold mt-1">TELEGRAM_WEBHOOK_SECRET</div>
    </div>
    <div class="p-4" style="background:{{ count($chatIds) ? '#a3e635' : '#fef3c7' }};border:3px solid #000;box-shadow:5px 5px 0 #000;">
        <div class="text-xs font-bold uppercase mb-1">Chat Broadcast</div>
        <div class="text-lg font-black">{{ count($chatIds) }} chat</div>
        <div class="text-[10px] font-bold mt-1">TELEGRAM_CHAT_IDS</div>
    </div>
</div>

{{-- Overview --}}
<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-2">Apa yang Bot Lakukan?</h2>
    <ul class="text-sm text-black list-disc list-inside space-y-1">
        <li><strong>Broadcast harian</strong> jam <code>{{ $dailyTime }}</code> WIB — kirim member ulang tahun & kelulusan bulan ini ke semua <code>TELEGRAM_CHAT_IDS</code>.</li>
        <li><strong>Slash command</strong> — user chat ke bot dan mendapat balasan otomatis (lihat daftar di bawah).</li>
    </ul>
</div>

{{-- Setup --}}
<div class="mb-6 p-5" style="background:#fff;border:3px solid #000;box-shadow:5px 5px 0 #000;">
    <h2 class="display text-black text-xl mb-3">Cara Setup</h2>

    <ol class="text-sm text-black space-y-4 list-decimal list-inside">
        <li>
            <strong>Buat bot lewat BotFather.</strong> Kirim <code>/newbot</code> ke
            <a class="underline" href="https://t.me/BotFather" target="_blank" rel="noopener">@BotFather</a>,
            ikuti prompt (nama & username), lalu salin <em>bot token</em> yang diberikan.
        </li>

        <li>
            <strong>Isi <code>.env</code>:</strong>
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=123456:ABC-DEF...
TELEGRAM_CHAT_IDS=-1001234567890,987654321
TELEGRAM_PARSE_MODE=HTML
TELEGRAM_WEBHOOK_SECRET=random-string-panjang-tanpa-spasi</pre>
        </li>

        <li>
            <strong>Ambil <code>chat_id</code>.</strong> Chat/broadcast ke bot dulu (bisa DM
            atau tambahkan bot ke grup), lalu buka di browser:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</pre>
            Cari nilai <code>chat.id</code> — masukkan ke <code>TELEGRAM_CHAT_IDS</code> (boleh multiple, pisah koma).
        </li>

        <li>
            <strong>Daftarkan webhook (untuk slash command).</strong> URL webhook aplikasi ini:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">{{ $webhookUrl }}</pre>
            Daftarkan ke Telegram (jalankan sekali dari terminal / browser):
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">curl "https://api.telegram.org/bot&lt;TOKEN&gt;/setWebhook?url={{ urlencode($webhookUrl) }}"</pre>
            Verifikasi berhasil: <code>{"ok":true,"result":true,"description":"Webhook was set"}</code>.
        </li>

        <li>
            <strong>(Opsional) Daftarkan menu command</strong> supaya muncul autocomplete di Telegram:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">curl -X POST "https://api.telegram.org/bot&lt;TOKEN&gt;/setMyCommands" \
  -H "Content-Type: application/json" \
  -d '{"commands":[
    {"command":"ultah","description":"Ulang tahun hari ini"},
    {"command":"lulus","description":"Kelulusan bulan ini"},
    {"command":"member","description":"Cari member (butuh nama)"},
    {"command":"jadwal","description":"Event bulan ini"},
    {"command":"help","description":"Bantuan"}
  ]}'</pre>
        </li>

        <li>
            <strong>Jadwalkan broadcast harian.</strong> Pastikan Laravel scheduler jalan di server:
<pre class="text-xs p-3 mt-2 overflow-x-auto" style="background:#0f172a;color:#e2e8f0;border:2px solid #000;">* * * * * cd /path/to/app && php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</pre>
            Broadcast akan otomatis terkirim tiap hari jam <code>{{ $dailyTime }}</code> (Asia/Jakarta).
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
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Alias</th>
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Deskripsi</th>
                    <th class="px-3 py-2 text-left" style="border:2px solid #000;">Contoh</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $commands = [
                        ['/ultah',    '/birthday',  'Daftar member yang ulang tahun HARI INI (status Aktif).', '/ultah'],
                        ['/lulus',    '/graduation','Daftar kelulusan bulan berjalan.',                        '/lulus'],
                        ['/member',   '/cari',      'Cari member berdasarkan nama/nickname/tempat lahir (max 5 hasil).', '/member shani'],
                        ['/jadwal',   '/kalender',  'Event bulan berjalan (ulang tahun, kelulusan, anniversary generasi).', '/jadwal'],
                        ['/help',     '/start',     'Menampilkan bantuan.',                                    '/help'],
                    ];
                @endphp
                @foreach ($commands as $c)
                    <tr>
                        <td class="px-3 py-2 font-mono font-bold" style="border:2px solid #000;background:#dbeafe;">{{ $c[0] }}</td>
                        <td class="px-3 py-2 font-mono" style="border:2px solid #000;">{{ $c[1] }}</td>
                        <td class="px-3 py-2" style="border:2px solid #000;">{{ $c[2] }}</td>
                        <td class="px-3 py-2 font-mono" style="border:2px solid #000;">{{ $c[3] }}</td>
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
            <div class="font-bold">❌ Bot tidak membalas apa pun.</div>
            <div>Periksa: (1) webhook terset? cek <code>https://api.telegram.org/bot&lt;TOKEN&gt;/getWebhookInfo</code>, (2) URL webhook HTTPS &amp; valid, (3) <code>TELEGRAM_WEBHOOK_SECRET</code> di URL sama dengan di <code>.env</code>.</div>
        </div>
        <div>
            <div class="font-bold">❌ 404 pada webhook URL.</div>
            <div>Secret di URL salah — value harus persis sama dengan <code>TELEGRAM_WEBHOOK_SECRET</code>.</div>
        </div>
        <div>
            <div class="font-bold">❌ Broadcast harian tidak muncul.</div>
            <div>Cek cron <code>schedule:run</code> aktif, dan <code>TELEGRAM_CHAT_IDS</code> tidak kosong. Uji manual: <code>php artisan notifications:daily</code>.</div>
        </div>
        <div>
            <div class="font-bold">ℹ️ Reset webhook</div>
            <div><code>curl "https://api.telegram.org/bot&lt;TOKEN&gt;/deleteWebhook"</code> lalu daftarkan ulang.</div>
        </div>
    </div>
</div>
@endsection
