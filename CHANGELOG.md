# Changelog

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.1.0/).
Tanggal dalam format `YYYY-MM-DD`.

---

## [Unreleased]

### Added

- **Generasi 14** — seeder `Generation14MemberSeeder` menambahkan 11 trainee (Afera Thalia, Carissa Dini, Christabella Bonita, Fahira Putri, Fatimah Azzahra, Heidi Suyangga, Maegan Jovanka, Maxine Faye, Putry Jazyta, Ralyne Van Irwan, Sona Kalyana). Status: `Aktif` + `restructure_status: Trainee`. `GenerationSeeder` juga di-update mencakup kode `14`. Member `join_date` di-inherit dari generasi (`gen->join_date`) saat seed.
- **`Member::effective_join_date` accessor** — fallback ke `generation.join_date` kalau kolom `join_date` member null. `ageAtJoin` & `daysInJkt48` sekarang pakai accessor ini — statistik tenure otomatis benar untuk member yang belum di-set join_date manual.
- **`Generation` model observer** — saat `Generation.join_date` di-set/di-ubah (via admin edit atau seeder), semua member generasi tsb yg `join_date` masih null otomatis di-backfill dgn tanggal generasi. Member yg join_date-nya sudah di-set eksplisit tidak diganggu.
- **`Member` saving hook** — otomatis set `status = 'Lulus'` saat `graduation_date` sudah <= hari ini. Member dgn `graduation_date` di masa depan tetap `Aktif` (announced-but-not-yet).
- **`php artisan members:sync-status`** — backfill: flip semua member `Aktif` yg `graduation_date` sudah lewat menjadi `Lulus`. Aman di-rerun.
- **`GraduationUpdateSeeder`** — update data kelulusan 9 member: Shani Indira Natio, Azizi Asadel, Reva Fidela, Indira Seruni, Shania Gracia, Amanda Sukma, Chelsea Davina, Cathleen Nixie, Alya Amanda. Isi `restructure_status` (JKT48/Team Dream/Team Love), `graduation_announce_date`, `graduation_announce_event`, `graduation_date`. Status Lulus di-flip otomatis via saving hook.
- **Foto member** (`public/img/*.jpg|webp|png`) — 257 file di-commit ke repo.
- **`php artisan members:match-photos`** — auto-match file di `public/img/` ke member berdasarkan tokens nama (case-insensitive, ignore prefix `Gen{N}_`). Set `photo_url = /img/FILENAME`. Opsi: `--dry-run` (report saja), `--overwrite` (replace existing), `--dir=X` (folder lain). Report: orphan files (no member match), ambiguous matches, member tanpa foto.
- Halaman **JKT48 Member Sorter** di `/sorter/member` — merge sort interaktif dgn:
  - Filter status (Aktif/Lulus) + generasi.
  - Undo 1 langkah, keyboard shortcut ← → ↓ (seri) U (undo).
  - Progress bar berdasarkan total elemen tree merge.
  - Toggle ranking hasil: Unik / Tie 1,1,2,3 / Tie 1,1,3,4.
  - Salin teks & screenshot PNG (html2canvas).
- Landing sorter `/sorter` — daftar tipe sorter, extensible ke lagu/setlist nanti.
- Link **Sorter** di nav bar utama.
- `composer.lock` di-commit — deploy Hostinger reproducible, menghindari corrupted-zip error.

### Fixed

- `.env.example` — kutip `APP_NAME` yang mengandung spasi (fix dotenv parse error).
- `DEPLOY.md` — typo domain admin members URL (`jkt48.rebornian48.id` → `jkt48.rebornian48.my.id`).

### Changed

- Arsitektur controller sorter type-dispatched via `data{Type}()` methods — plug-in sorter baru tanpa duplikasi.
- Tombol **Undo** kedua di kolom tengah dihapus lagi — cukup satu Undo di header (dekat Restart). User sudah familiar posisinya.
- **Neobrutalism UI** diterapkan ke **semua halaman** (dashboard, members, singles, captains, admin, login, sorter):
  - Global stylesheet `public/css/neo.css` — di-load dari `layouts/app.blade.php`, `layouts/admin.blade.php`, dan `auth/login.blade.php`.
  - Border hitam 3px seragam, hard offset shadow `4-6px 0 #000`, palet cream/kuning/pink/lime tanpa gradien.
  - Tipografi display *Archivo Black* (uppercase headings) + body *Space Grotesk*.
  - Press-animation `translate(4/6px)` dgn shadow hilang; reduced-motion honored.
  - Focus ring cyan dashed 3px distinct dari border dekoratif.
  - Dark mode toggle dihapus (design light-only).
  - Kontras teks 4.5:1+: teks hitam di semua fill saturated.

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
