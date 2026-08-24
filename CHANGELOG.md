# Changelog

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.1.0/).
Tanggal dalam format `YYYY-MM-DD`.

---

## [Unreleased]

### Added
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
- Tombol **Undo** kedua ditambahkan di kolom tengah panel sorter (di atas tombol Seri) — akses lebih dekat ke area comparison, tanpa menghilangkan Undo di header.

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
