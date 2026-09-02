# JKT48 Database

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/77054fe29cb445838dcf24018b94f6f7)](https://app.codacy.com/gh/Rebornian48/data/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![CodeFactor](https://www.codefactor.io/repository/github/rebornian48/data/badge)](https://www.codefactor.io/repository/github/rebornian48/data)

Aplikasi web berbasis Laravel untuk mengelola database JKT48 — member, generasi, single (senbatsu), team, riwayat kapten, peta member — dengan dashboard publik, panel admin, sorter interaktif, REST API terbuka, dan bot notifikasi (Telegram + Discord).

Live: <https://jkt48.rebornian48.my.id>
API docs: <https://jkt48.rebornian48.my.id/api/docs>

---

## Fitur

### Dashboard publik (`/`)

- Statistik ringkas: total member, aktif, lulus, generasi, singles.
- Chart pertumbuhan member harian (Chart.js).
- Top 10 tenure, top senbatsu, top center.
- Distribusi usia & birthplace.
- Kapten aktif per posisi.

### Daftar member (`/members`)

- Grid kartu member dgn foto, generasi, status.
- Filter: search text, generasi, status. Pagination.

### Detail member (`/members/{id}`)

- Profil lengkap, timeline karier, daftar single, riwayat kapten, riwayat team.

### Singles (`/singles`)

- List semua single dgn jumlah member per single.

### Captains (`/captains`)

- Timeline kapten per posisi (chart) + riwayat berurutan.
- Kapten sekarang linked ke team (Team J / KIII / T / dst).

### Statistik (`/statistik`)

- Tabel gabungan per-generasi: jumlah member aktif/lulus, rata-rata usia sekarang, rata-rata usia saat join, rata-rata masa aktif (untuk yg lulus), dan rata-rata "days to graduate".
- Baris khusus **Kaigai dan Transfer** (Kaigai 1 + Kaigai 2 + Transfer digabung).
- Baris **New Era survivors** (exclude Gen 10).
- Split **JKT48V Gen 1** vs **Gen 2**.
- Fallback ke `Generation.join_date` bila member belum di-set manual.

### Restrukturisasi (`/restrukturisasi`)

- Ringkasan hasil restrukturisasi: distribusi per `restructure_status` (JKT48 / Team Dream / Team Love / Trainee / dsb.), termasuk yg diumumkan lulus.

### Kalender (`/kalender`)

- Kalender event bulanan: ulang tahun semua member (aktif + lulus), tanggal announce & tanggal lulus.
- Setiap event nampilkan usia member saat kejadian.

### Peta JKT48 (`/peta`, `/peta/{slug}`)

- Peta interaktif berbasis Leaflet: sebaran birthplace member, titik/polygon/polyline custom, catatan per titik.
- Data disimpan di master `maps` + `map_points` / `map_polylines` / `map_polygon_layers` / `map_notes`.
- Bisa di-seed dari Google Sheets via `JKT48MapSeeder` (butuh `GOOGLE_SHEETS_API_KEY` + `JKT48_MAP_SHEET_ID`).
- Endpoint JSON: `GET /api/peta/{slug}` (dipakai frontend map, bukan bagian dari REST API publik).

### Sorter interaktif (`/sorter`)

- **Neobrutalism UI** — border hitam tebal, hard shadow, palet cream/kuning/pink/lime, tipografi display *Archivo Black*.
- **Member sorter** (`/sorter/member`) — merge sort interaktif berbasis perbandingan berpasangan.
  - Filter status (Aktif/Lulus) + generasi.
  - Pilih Kiri / Kanan / Seri; keyboard shortcut ← → ↓ U.
  - Progress bar + undo 1 langkah (tombol di header & di kolom tengah, dekat Seri).
  - Hasil: toggle ranking Unik / Tie 1,1,2,3 / Tie 1,1,3,4.
  - Salin teks, screenshot PNG (html2canvas).
- Arsitektur extensible — tinggal tambah tipe (`song`, `setlist`, dst) di `SorterController`.

### REST API v1 (`/api/v1/*`)

Read-only JSON API, JSON:API-ish envelope (`data` + `meta` pagination). No auth (public dataset). Base URL: `https://jkt48.rebornian48.my.id/api`.

| Endpoint | Fungsi |
|---|---|
| `GET /api/v1/members` | List member (filter: `q`, `status`, `generation_id`, `team_id`; pagination `page`, `per_page`) |
| `GET /api/v1/members/{id}` | Detail member + relasi (singles, captains, teams) |
| `GET /api/v1/singles` | List single + jumlah member |
| `GET /api/v1/singles/{id}` | Detail single + tracklist member |
| `GET /api/v1/generations` | List generasi |
| `GET /api/v1/generations/{id}` | Detail generasi + member |
| `GET /api/v1/teams` | List team |
| `GET /api/v1/teams/{id}` | Detail team + member aktif |
| `GET /api/v1/captains` | List kapten (per posisi/team) |
| `GET /api/v1/statistics` | Statistik gabungan per generasi |

**Swagger UI**: <https://jkt48.rebornian48.my.id/api/docs> — spec OpenAPI di `/api/docs/openapi.json`.

### Bot notifikasi (Telegram + Discord)

Broadcast otomatis ke Telegram dan/atau Discord ketika ada:

- Ulang tahun member aktif (per hari).
- Kelulusan member (per hari, berdasarkan `graduation_date`).

Dedupe pakai tabel `notification_logs` — aman kalau scheduler jalan berkali-kali di hari yg sama.

Selain broadcast, bot juga menerima command inbound:

| Command | Fungsi |
|---|---|
| `/help` | Bantuan |
| `/ultah` | Member yang berulang tahun hari ini |
| `/lulus` | Kelulusan bulan ini |
| `/member <nama>` | Cari member (max 5 hasil) |
| `/jadwal` | Semua event bulan ini |

Nambah command baru: tambah case di `App\Services\Notifications\CommandRouter::handle()` — otomatis jalan di Telegram maupun Discord.

**Setup singkat:**

1. Isi `.env` (lihat blok `Bot notifications` di `.env.example`).
2. `php artisan migrate` — buat tabel `notification_logs`.
3. Aktifkan cron scheduler (Hostinger cron 1 menit sekali):
   ```
   * * * * * cd ~/domains/jkt48.rebornian48.my.id/public_html && php artisan schedule:run >> /dev/null 2>&1
   ```
4. **Telegram**: set webhook agar bot menerima command:
   ```
   curl "https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://<APP_URL>/webhooks/telegram/<TELEGRAM_WEBHOOK_SECRET>"
   ```
5. **Discord (webhook broadcast)**: cukup buat Incoming Webhook di channel → paste URL ke `DISCORD_WEBHOOK_URLS`.
6. **Discord (slash command opsional)**: di Developer Portal → set Interactions Endpoint URL ke `https://<APP_URL>/webhooks/discord`, isi `DISCORD_PUBLIC_KEY`, lalu register command (`/ultah`, `/lulus`, `/member`, `/jadwal`, `/help`) via API. Signature Ed25519 diverifikasi di controller.

Manual test broadcast hari ini:
```
php artisan notifications:daily
php artisan notifications:daily --date=2026-09-15
```

### Panel admin (`/admin`)

- Auth berbasis session dgn `admin_users` table (bcrypt). Default: `admin` / `data_jkt48`.
- CRUD: members, singles, generations, captains, **teams**, **maps** (peta).
- **Ganti password** (`/admin/password`).
- **Dokumentasi in-app** (`/admin/docs/{api,telegram,discord}`) — panduan API, setup bot Telegram, setup bot Discord.

---

## Tech Stack

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Blade + Tailwind CSS (CDN) + Chart.js + Leaflet (peta)
- **Style**: Neobrutalism global (`public/css/neo.css`) — cream ground, chunky borders, hard offset shadows, *Archivo Black* headings.
- **DB**: MySQL / MariaDB
- **API docs**: Swagger UI (spec OpenAPI 3.0 di `App\Http\Controllers\Api\DocsController`).
- **Deployment**: Hostinger shared hosting via Git auto-deploy

---

## Struktur Proyek

```
jkt48_data/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php     # halaman publik
│   │   ├── LoginController.php
│   │   ├── PetaController.php          # peta publik
│   │   ├── SorterController.php        # sorter (extensible per type)
│   │   ├── Admin/                      # CRUD admin + docs + password
│   │   ├── Api/                        # REST API v1 + Swagger docs
│   │   └── Webhooks/                   # Telegram + Discord inbound
│   ├── Models/
│   │   ├── Member.php  Generation.php  Single.php  Captain.php
│   │   ├── Team.php    MemberTeam.php  AdminUser.php
│   │   └── Map.php     MapPoint.php    MapPolyline.php ...
│   └── Services/Notifications/         # Broadcaster + CommandRouter
├── database/
│   ├── migrations/                     # schema Laravel
│   └── seeders/                        # generasi, member, single, team,
│                                       # captain, admin user, map
├── resources/views/
│   ├── layouts/       app.blade.php   admin.blade.php
│   ├── dashboard/     index members member singles captains
│   │                  statistik restrukturisasi calendar
│   ├── admin/         CRUD + docs + password
│   ├── auth/          login
│   ├── peta/          peta interaktif
│   └── sorter/        index / member
├── public/
│   ├── css/neo.css                     # global Neobrutalism overrides
│   ├── js/sorter-member.js             # algoritma merge sort interaktif
│   ├── img/                            # foto member (257 file)
│   └── .htaccess                       # Laravel front-controller
├── routes/
│   ├── web.php                         # halaman + admin + webhooks
│   └── api.php                         # REST API v1 + Swagger
├── composer.json  composer.lock
├── schema.sql                          # raw SQL (setup manual)
├── DEPLOY.md                           # panduan deploy Hostinger
├── INSTALL.md                          # setup lokal
└── CHANGELOG.md
```

---

## Setup Lokal

Lihat [INSTALL.md](INSTALL.md).

Ringkas:

```bash
git clone https://github.com/Rebornian48/data.git jkt48_data
cd jkt48_data
composer install
cp .env.example .env
php artisan key:generate
# edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan migrate --seed
php artisan serve
```

---

## Deploy

Lihat [DEPLOY.md](DEPLOY.md).

Ringkas:

1. Hostinger hPanel → Git → connect repo GitHub `Rebornian48/data`.
2. Branch `main`, deploy path `public_html`.
3. Set document root ke `.../public_html/public` (Laravel front-controller).
4. Setelah first deploy, SSH ke server:
   ```bash
   cd ~/domains/jkt48.rebornian48.my.id/public_html
   cp .env.example .env
   php artisan key:generate --force
   # edit .env: DB_*, APP_URL, APP_ENV=production, APP_DEBUG=false, bot vars
   mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   php artisan migrate --seed --force
   php artisan config:clear
   ```
5. Tambahkan cron 1 menit untuk scheduler (broadcast ulang tahun/kelulusan).
6. Enable **Deploy otomatis** — push berikutnya trigger sendiri.

---

## Extensibility Sorter

Nambah sorter baru (contoh `song`):

1. `SorterController.php` — tambah `'song'` ke `SUPPORTED_TYPES`, buat method `dataSong()` yang return array dgn key: `sorterTitle`, `sorterSubtitle`, `items` (schema `{id, name, photo, ...}`).
2. Copy `resources/views/sorter/member.blade.php` → `song.blade.php`, sesuaikan filter panel.
3. Reuse `public/js/sorter-member.js` (atau generalize jadi `sorter-core.js` kalau logic sama persis).
4. URL `/sorter/song` otomatis jalan lewat route dinamis existing.

---

## Kredensial Admin Default

```
username: admin
password: data_jkt48
```

Ganti setelah deploy pertama via **Admin → Ganti Password** (`/admin/password`).

---

## Lisensi

Proyek internal — tidak untuk redistribusi tanpa izin.
