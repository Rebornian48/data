# Changelog

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.1.0/).
Tanggal dalam format `YYYY-MM-DD`.

---

## [Unreleased]

### Added

- **Integrasi data `Diskografi.xlsx`** — 7 sheet dinormalisasi jadi 8 tabel baru + kolom baru di `singles`:
  - `singles` bertambah kolom: `title_jp`, `origin_group`, `release_year`, `mv_title`, `mv_url`, `cover_art_url`, `audio_file`.
  - Tabel baru: `songs` (425 lagu master), `albums` + `album_tracks` (5 studio + 1 EP + tracklist ter-link ke `songs`), `sub_units` + `sub_unit_songs` (5 unit, 23 lagu), `coupling_songs` + `coupling_song_members` (11 lagu B-side dengan senbatsu di-link ke `members`), `setlists` + `setlist_songs` (17 reguler + 10 special), `mv_locations` (44 baris lokasi syuting).
  - Sumber JSON di `database/data/diskografi/*.json` (di-parse dari Excel via `scratchpad/parse_diskografi.py`), dieksekusi oleh `DiskografiSeeder` yang idempotent.
  - Alias baru untuk resolusi member: `Vienny → Viny`, plus `Cindy → Cindy Hapsari` dan `Ella → Gabriela Abigail Mewengkang` untuk kasus nickname ambigu di coupling.
- **Halaman detail single diperkaya** — `/singles/{id}` sekarang menampilkan cover art, judul JP, asal grup, section "Daftar Lagu" (dengan link preview YouTube), dan section "Coupling Songs" (center + member list).
- **Halaman Album & EP** (`/albums`, `/albums/{id}`) — grid berkelompok Album Studio vs Mini Album/EP, halaman detail dengan tracklist ter-link ke katalog `songs` (judul asal, asal grup, link preview YouTube).
- **Halaman Setlist** (`/setlists`, `/setlists/{id}`) — grid berkelompok reguler vs special, halaman detail dengan daftar lagu berurutan (kolom judul asal, asal grup, single terkait, link preview YouTube).
- Menu navigasi utama ditambah link **ALBUM** dan **SETLIST**.

---

## 2026-09-03

### Added

- **Halaman detail single** (`/singles/{id}`) — klik kartu single di `/singles` untuk membuka daftar senbatsu lengkap dengan foto + nama + generasi. Center ditampilkan di section terpisah paling atas (border merah + badge "CENTER" di kartu). Kedua section diurutkan berdasarkan nama lengkap (A–Z).
- **`MemberSingleSeeder`** — isi tabel pivot `member_singles` untuk S1–S27 (center + senbatsu) berdasarkan data dari spreadsheet JKT48. Kolom `role` = `center` / `member`, `position` menyimpan urutan asli dari spreadsheet. Idempotent per single.
  - Alias-map otomatis dari nickname spreadsheet ke DB: Aki→Akicha, Shania→Shanju, Yuvi→Yupi, Thalia→Tata, Chikano→Chikarina, Azizi→Zee, Pucchi→Puti, Meme→Melati, Mira→Amira.
  - Name overrides untuk nickname ambigu: CinHap→Cindy Hapsari (Gen 4), Indah→Indah Cahya (Gen 9).
- **5 single baru** di `SingleSeeder` — S23 Flying High, S24 Sayonara Crawl, S25 Magic Hour, S26 #SukiNanda, S27 Idol Nanka Janakattara. Untuk yang tanggal rilisnya belum diketahui, `release_date` = null dan tahun disimpan di kolom `notes`.

### Fixed

- Dashboard singles (`/singles`) tidak lagi crash 500 ketika `release_date` null — fallback ke `notes` atau `TBD`.

---

## 2026-09-02

### Added

- **Dokumentasi in-app di panel admin** — halaman `/admin/docs/api`, `/admin/docs/telegram`, `/admin/docs/discord`. Panduan REST API v1, cara setup bot Telegram (webhook + set commands), cara setup bot Discord (webhook broadcast + slash command via Interactions Endpoint). Menu langsung di sidebar admin.

---

## 2026-09-01

### Added

- **REST API v1** (`/api/v1/*`) — read-only JSON endpoints untuk resource publik:
  - `GET /api/v1/members` (filter `q`, `status`, `generation_id`, `team_id`; pagination `page`, `per_page`).
  - `GET /api/v1/members/{id}` (dgn relasi singles, captains, teams).
  - `GET /api/v1/singles` + `/singles/{id}`.
  - `GET /api/v1/generations` + `/generations/{id}`.
  - `GET /api/v1/teams` + `/teams/{id}`.
  - `GET /api/v1/captains`.
  - `GET /api/v1/statistics` — statistik gabungan per generasi.
- **Swagger UI** di `/api/docs`, spec OpenAPI 3.0 di `/api/docs/openapi.json` (di-generate di `App\Http\Controllers\Api\DocsController`).
- Envelope respons konsisten `{ data, meta }`; pagination Laravel default.
- **Bot notifikasi Telegram + Discord**:
  - Broadcast harian: ulang tahun member aktif + kelulusan (per `graduation_date`).
  - Dedupe pakai tabel baru `notification_logs` — aman kalau scheduler jalan berkali-kali.
  - Inbound command: `/ultah`, `/lulus`, `/member <nama>`, `/jadwal`, `/help`. Router shared di `App\Services\Notifications\CommandRouter` — sekali tambah command, jalan di Telegram maupun Discord.
  - Telegram: webhook di `POST /webhooks/telegram/{secret}`, kirim command via `sendMessage` API.
  - Discord: broadcast via Incoming Webhook (multiple URLs, comma-separated). Slash command via Interactions Endpoint (`POST /webhooks/discord`) dgn verifikasi signature Ed25519.
  - Artisan: `php artisan notifications:daily [--date=YYYY-MM-DD]` (dijadwalkan lewat `NOTIFICATIONS_DAILY_TIME`, default 08:00 Asia/Jakarta).
- `.env.example` — blok `Bot notifications` (`TELEGRAM_*`, `DISCORD_*`, `NOTIFICATIONS_DAILY_TIME`).

### Changed

- **Statistik** (`/statistik`): tambah baris merged **Kaigai dan Transfer** (Kaigai 1 + Kaigai 2 + Transfer) supaya perbandingan generasi lebih rapat.

---

## 2026-08-31

### Added

- **Teams module**:
  - Master data `teams` (Team J, Team KIII, Team T, dst.) — CRUD di admin (`/admin/teams`).
  - `member_teams` pivot dgn `joined_at` / `left_at` untuk melacak history perpindahan member antar team.
  - Kolom `team_id` di `captains` — kapten sekarang linked ke team-nya.
  - Ditampilkan di detail member + halaman captains.

### Fixed

- **Statistik** — generasi yg member-nya tidak punya data usia/join_date tetap muncul di tabel (sebelumnya hilang total). Placeholder ditampilkan bila data kosong.
- **Statistik** — chart active-count fixed; hitungan "days to graduate" muncul di kolom tersendiri.

### Changed

- **Statistik** — usia ditampilkan dgn desimal + suffix "tahun".
- **Statistik** — konversi ke tabel gabungan compact (satu baris per generasi).
- **Statistik** — JKT48V dipecah jadi **Virtual Gen 1** dan **Virtual Gen 2**.
- **Statistik** — fallback ke `Generation.join_date` dari DB kalau kolom generasi seeder tidak set; placeholder untuk yg missing.
- **Statistik** — exclude Generasi 10 dari kelompok "New Era survivors".

---

## 2026-08-29

### Added

- **Peta JKT48** (`/peta`, `/peta/{slug}`) — peta interaktif berbasis Leaflet:
  - Master `maps` + `map_settings` + `map_points` + `map_polylines` + `map_polygon_layers` + `map_polygon_settings` + `map_notes`.
  - Endpoint JSON `GET /api/peta/{slug}` untuk frontend map.
  - Admin CRUD di `/admin/maps`.
  - Seeder `JKT48MapSeeder` — import dari Google Sheets (butuh `GOOGLE_SHEETS_API_KEY` + `JKT48_MAP_SHEET_ID`).
- **Admin change password** — halaman `/admin/password` dgn validasi current password.
- **AdminUser model + `admin_users` table** — auth admin pindah dari user hard-coded ke DB, bcrypt hashed.

### Changed

- **Kalender** (`/kalender`) — sekarang mencakup ulang tahun **semua** member (aktif + lulus), bukan cuma aktif. Setiap event menampilkan usia member pada saat kejadian.
- `Member::photo_url` — izinkan path relatif (bukan cuma URL absolut).

### Fixed

- Runtime files (`storage/framework/{sessions,cache,views}`) di-ignore di git.

---

## 2026-08-28

### Added

- **Statistik** (`/statistik`) — halaman baru dgn tabel per-generasi (count member, rata-rata usia, rata-rata masa aktif).
- **Restrukturisasi** (`/restrukturisasi`) — ringkasan distribusi `restructure_status` (JKT48 / Team Dream / Team Love / Trainee / dsb).
- **Foto member** (`public/img/*.jpg|webp|png`) — 257 file di-commit ke repo.
- **`php artisan members:match-photos`** — auto-match file di `public/img/` ke member berdasarkan tokens nama (case-insensitive, ignore prefix `Gen{N}_`). Set `photo_url = /img/FILENAME`. Opsi: `--dry-run`, `--overwrite`, `--dir=X`. Report: orphan files, ambiguous matches, member tanpa foto.
- **Generasi 14** — seeder `Generation14MemberSeeder` menambahkan 11 trainee (Afera Thalia, Carissa Dini, Christabella Bonita, Fahira Putri, Fatimah Azzahra, Heidi Suyangga, Maegan Jovanka, Maxine Faye, Putry Jazyta, Ralyne Van Irwan, Sona Kalyana). Status: `Aktif` + `restructure_status: Trainee`. `GenerationSeeder` juga di-update mencakup kode `14`. Member `join_date` di-inherit dari generasi.
- **`Member::effective_join_date` accessor** — fallback ke `generation.join_date` kalau kolom `join_date` member null. `ageAtJoin` & `daysInJkt48` sekarang pakai accessor ini.
- **`Generation` model observer** — saat `Generation.join_date` di-set/di-ubah, semua member generasi tsb yg `join_date` masih null otomatis di-backfill.
- **`Member` saving hook** — otomatis set `status = 'Lulus'` saat `graduation_date` sudah <= hari ini. Member dgn `graduation_date` di masa depan tetap `Aktif`.
- **`php artisan members:sync-status`** — backfill: flip semua member `Aktif` yg `graduation_date` sudah lewat menjadi `Lulus`. Aman di-rerun.
- **`GraduationUpdateSeeder`** — update data kelulusan 9 member (Shani, Azizi, Reva, Indira Seruni, Shania, Amanda Sukma, Chelsea, Cathleen, Alya Amanda). Isi `restructure_status`, `graduation_announce_date`, `graduation_announce_event`, `graduation_date`.
- Halaman **JKT48 Member Sorter** di `/sorter/member` — merge sort interaktif:
  - Filter status (Aktif/Lulus) + generasi.
  - Undo 1 langkah, keyboard shortcut ← → ↓ (seri) U (undo).
  - Progress bar berdasarkan total elemen tree merge.
  - Toggle ranking hasil: Unik / Tie 1,1,2,3 / Tie 1,1,3,4.
  - Salin teks & screenshot PNG (html2canvas).
- Landing sorter `/sorter` — daftar tipe sorter, extensible ke lagu/setlist nanti.
- Link **Sorter** di nav bar utama.
- `composer.lock` di-commit — deploy Hostinger reproducible.
- Codacy + CodeFactor badges di README.

### Changed

- Arsitektur controller sorter type-dispatched via `data{Type}()` methods — plug-in sorter baru tanpa duplikasi.
- **Neobrutalism UI** diterapkan ke **semua halaman** (dashboard, members, singles, captains, admin, login, sorter):
  - Global stylesheet `public/css/neo.css` — di-load dari `layouts/app.blade.php`, `layouts/admin.blade.php`, `auth/login.blade.php`.
  - Border hitam 3px seragam, hard offset shadow `4-6px 0 #000`, palet cream/kuning/pink/lime tanpa gradien.
  - Tipografi display *Archivo Black* (uppercase headings) + body *Space Grotesk*.
  - Press-animation `translate(4/6px)` dgn shadow hilang; reduced-motion honored.
  - Focus ring cyan dashed 3px distinct dari border dekoratif.
  - Dark mode toggle dihapus (design light-only).
  - Kontras teks 4.5:1+: teks hitam di semua fill saturated.
- Codacy config — exclude `app`, `public`, `routes` dari analysis.

### Fixed

- `.env.example` — kutip `APP_NAME` yang mengandung spasi (fix dotenv parse error).
- `DEPLOY.md` — typo domain admin members URL (`jkt48.rebornian48.id` → `jkt48.rebornian48.my.id`).

---

## 2026-08-24

### Changed

- Root `.htaccess` diganti dgn PHP redirect `index.php` → arahkan visitor ke `/public/` (subdir Laravel).
- Root `.htaccess` sebelumnya di-fix agar hanya rewrite empty path (bukan semua request).

---

## 2026-08-23

### Added

- Halaman publik **Captains** (`/captains`) dgn timeline chart per posisi.

### Changed

- Growth chart di dashboard: dari kumulatif menjadi **daily active members**.

### Fixed

- Admin create views — pass empty model instances agar tidak error di form.

---

## 2026-08-22

### Added

- Halaman **Singles** publik (`/singles`).
- Dark mode toggle di nav bar (Alpine.js + localStorage).
- Growth chart pertumbuhan member di dashboard.

### Changed

- Auth admin disederhanakan ke session-based (menghilangkan 500 error dari middleware sebelumnya).

---

## 2026-08-21

### Added

- Rilis awal: Laravel 13 app dgn dashboard publik, daftar member, detail member, panel admin (CRUD members/singles/generations/captains).
- Login admin default `admin` / `data_jkt48`.
- Schema DB: `generations`, `singles`, `members`, `member_singles`, `captains`.
- Seeder import dari Excel (PhpSpreadsheet).
- `.htaccess` untuk URL rewriting Laravel.
- Panduan `INSTALL.md` (setup lokal) & `DEPLOY.md` (Hostinger).

### Fixed

- `config/app.php` kompatibel dgn Laravel 13.
- Remove Tinker (Laravel 13 compat).
