# JKT48 Database

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/77054fe29cb445838dcf24018b94f6f7)](https://app.codacy.com/gh/Rebornian48/data/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![CodeFactor](https://www.codefactor.io/repository/github/rebornian48/data/badge)](https://www.codefactor.io/repository/github/rebornian48/data)

Aplikasi web berbasis Laravel untuk mengelola database JKT48 — member, generasi, single (senbatsu), dan riwayat kapten — dengan dashboard publik, panel admin, serta sorter interaktif.

Live: <https://jkt48.rebornian48.my.id>

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

- Profil lengkap, timeline karier, daftar single, riwayat kapten.

### Singles (`/singles`)

- List semua single dgn jumlah member per single.

### Captains (`/captains`)

- Timeline kapten per posisi (chart) + riwayat berurutan.

### Sorter interaktif (`/sorter`)

- **Neobrutalism UI** — border hitam tebal, hard shadow, palet cream/kuning/pink/lime, tipografi display *Archivo Black*.
- **Member sorter** (`/sorter/member`) — merge sort interaktif berbasis perbandingan berpasangan.
  - Filter status (Aktif/Lulus) + generasi.
  - Pilih Kiri / Kanan / Seri; keyboard shortcut ← → ↓ U.
  - Progress bar + undo 1 langkah (tombol di header & di kolom tengah, dekat Seri).
  - Hasil: toggle ranking Unik / Tie 1,1,2,3 / Tie 1,1,3,4.
  - Salin teks, screenshot PNG (html2canvas).
- Arsitektur extensible — tinggal tambah tipe (`song`, `setlist`, dst) di `SorterController`.

### Panel admin (`/admin`)

- Auth berbasis session (admin/data_jkt48 default).
- CRUD members, singles, generations, captains.

---

## Tech Stack

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Blade + Tailwind CSS (CDN) + Chart.js
- **Style**: Neobrutalism global (`public/css/neo.css`) — cream ground, chunky borders, hard offset shadows, *Archivo Black* headings.
- **DB**: MySQL
- **Deployment**: Hostinger shared hosting via Git auto-deploy

---

## Struktur Proyek

```
jkt48_data/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php     # halaman publik
│   │   ├── LoginController.php
│   │   ├── SorterController.php        # sorter (extensible per type)
│   │   └── Admin/                      # CRUD admin
│   └── Models/
│       ├── Member.php   Generation.php   Single.php   Captain.php
├── database/
│   ├── migrations/                     # schema Laravel
│   └── seeders/                        # import dari Excel
├── resources/views/
│   ├── layouts/         app.blade.php   admin.blade.php
│   ├── dashboard/       index / members / member / singles / captains
│   ├── admin/           CRUD per entitas
│   ├── auth/            login
│   └── sorter/          index / member
├── public/
│   ├── css/neo.css                     # global Neobrutalism overrides
│   ├── js/sorter-member.js             # algoritma merge sort interaktif
│   └── .htaccess                       # Laravel front-controller
├── routes/web.php
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
git clone git@github.com:Rebornian48/data.git jkt48_data
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
3. Setelah first deploy, SSH ke server:
   ```bash
   cd ~/domains/jkt48.rebornian48.my.id/public_html
   cp .env.example .env
   php artisan key:generate --force
   # edit .env: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL, APP_ENV=production, APP_DEBUG=false
   mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   php artisan migrate
   php artisan config:clear
   ```
4. Enable **Deploy otomatis** — push berikutnya trigger sendiri.

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

Ganti setelah deploy pertama — lihat `LoginController`.

---

## Lisensi

Proyek internal — tidak untuk redistribusi tanpa izin.
