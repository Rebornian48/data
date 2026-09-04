# Panduan Install & Setup Lokal

## Prerequisites

- **PHP 8.3+** (composer minimum `^8.3`) — <https://www.php.net/>
- **Composer 2** — <https://getcomposer.org/>
- **Node.js 18+** (opsional — hanya kalau mau ubah Tailwind/JS build) — <https://nodejs.org/>
- **MySQL 8 / MariaDB 10.5+** — <https://www.mysql.com/>
- **Git** — <https://git-scm.com/>

Ekstensi PHP yang wajib: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `xml`, `gd` (untuk `PhpSpreadsheet` di seeder), `zip`.

---

## Quick Start

### 1. Clone Repository

```bash
git clone https://github.com/Rebornian48/data.git jkt48_data
cd jkt48_data
```

### 2. Jalankan Setup Script

```bash
# Linux/Mac
chmod +x setup.sh
./setup.sh

# Windows (Git Bash)
bash setup.sh

# Windows (PowerShell)
./setup.ps1
```

### 3. Manual Setup (Alternatif)

```bash
composer install
cp .env.example .env
php artisan key:generate

# edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD

php artisan migrate --seed
```

Frontend pakai Tailwind CDN + Chart.js CDN — **tidak perlu `npm install` / `npm run build`** kecuali mau ubah asset build pipeline.

---

## Konfigurasi Database

### 1. Buat Database

```sql
CREATE DATABASE jkt48_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Edit `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jkt48_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrate + Seed

```bash
php artisan migrate --seed
```

Seeder `DatabaseSeeder` akan menjalankan berurutan:

1. `GenerationSeeder` — generasi 0 s/d 14.
2. `SingleSeeder` — daftar single S1–S27 (S23–S27 belum ada tanggal rilis pasti; tahun disimpan di kolom `notes`).
3. `MemberSeeder` — data member (butuh `database/seeders/data/JKT48_Database.xlsx` jika Excel-based; kalau tidak ada, seeder skip).
4. `Generation14MemberSeeder` — 11 trainee Gen 14.
5. `TeamSeeder` — master team (J, KIII, T, dst.).
6. `MemberTeamSeeder` — history perpindahan member antar team.
7. `CaptainSeeder` — riwayat kapten.
8. `MemberSingleSeeder` — isi pivot `member_singles` (center + senbatsu S1–S27) berdasarkan data spreadsheet. Idempotent per single.
9. `AdminUserSeeder` — bikin user admin default (`admin` / `data_jkt48`).
10. `DiskografiSeeder` — import katalog diskografi dari `database/data/diskografi/*.json`: 425 lagu (`songs`), 5 studio album + 1 EP (`albums`/`album_tracks`), 5 sub-unit (`sub_units`/`sub_unit_songs`), 11 coupling songs (`coupling_songs`/`coupling_song_members`), 17 setlist reguler + 10 special (`setlists`/`setlist_songs`), 44 lokasi MV (`mv_locations`). Idempotent — aman di-rerun. Alias member: `Vienny→Viny`, `Cindy→Cindy Hapsari`, `Ella→Gabriela Abigail Mewengkang`.

### 4. (Opsional) Peta dari Google Sheets

```env
GOOGLE_SHEETS_API_KEY=AIza...
JKT48_MAP_SHEET_ID=1FinIC52jFCi5fL7oN5qZ-BKocZvBfxbiCTrzXetnANo
```

```bash
php artisan db:seed --class=JKT48MapSeeder
```

### 5. (Opsional) Re-seed Diskografi

Idempotent — aman di-rerun setelah data JSON di-update:

```bash
php artisan db:seed --class=DiskografiSeeder
```

---

## Jalankan Server

```bash
php artisan serve
```

Buka:

- Dashboard: <http://localhost:8000>
- Members / Singles / Albums / Setlists: `/members`, `/singles`, `/albums`, `/setlists`
- Sorter: `/sorter/member`, `/sorter/song`
- Admin: <http://localhost:8000/admin> — login `admin` / `data_jkt48`
- API docs (Swagger): <http://localhost:8000/api/docs>
- REST API v1: <http://localhost:8000/api/v1/members> — atau `/songs`, `/albums`, `/setlists`, `/coupling-songs`, `/sub-units`, `/mv-locations`

> **JKT48 Data API** disusun oleh **Rebornian48**.

---

## Kredensial Admin

Default seeder `AdminUserSeeder`:

```
username: admin
password: data_jkt48
```

Ganti setelah login pertama: menu **Admin → Ganti Password** (`/admin/password`).

Nambah admin baru via tinker:

```bash
php artisan tinker
```

```php
App\Models\AdminUser::create([
    'username' => 'user_baru',
    'password' => bcrypt('rahasia'),
]);
```

> Catatan: proyek ini **tidak** memakai Laravel Breeze/Fortify/User model. Auth admin sepenuhnya di `App\Models\AdminUser` + middleware `admin.auth`.

---

## Setup Bot Notifikasi (Opsional)

Kalau mau uji broadcast Telegram/Discord dari lokal, isi `.env`:

```env
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=123456:ABC...
TELEGRAM_CHAT_IDS=123456789
TELEGRAM_WEBHOOK_SECRET=random_string

DISCORD_ENABLED=true
DISCORD_WEBHOOK_URLS=https://discord.com/api/webhooks/...

NOTIFICATIONS_DAILY_TIME=08:00
```

Test broadcast tanpa nunggu scheduler:

```bash
php artisan notifications:daily                       # untuk hari ini
php artisan notifications:daily --date=2026-09-15     # untuk tanggal tertentu
```

Detail lengkap (webhook, slash command, cron) ada di README bagian **Bot notifikasi**.

---

## Schema SQL (Tanpa Laravel)

Kalau cuma butuh raw database:

```bash
mysql -u root -p jkt48_db < schema.sql
```

---

## Testing

```bash
# PHPUnit (bila ada suite)
php artisan test

# Atau Pest (bila terpasang)
./vendor/bin/pest
```

---

## Common Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Migration ulang (destructive!)
php artisan migrate:fresh --seed

# Sync status member (flip Aktif -> Lulus bila graduation_date sudah lewat)
php artisan members:sync-status

# Auto-match foto di public/img/ ke member
php artisan members:match-photos --dry-run
php artisan members:match-photos --overwrite

# Broadcast harian manual
php artisan notifications:daily
```

---

## Troubleshooting

### Permission denied (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

### Database connection failed

```bash
cat .env | grep DB_

php artisan tinker
>>> DB::connection()->getPdo();
```

### Class not found

```bash
composer dump-autoload
```

### Foto member tidak muncul

```bash
# Cek path relatif atau absolut di kolom photo_url
php artisan tinker
>>> App\Models\Member::whereNull('photo_url')->count();

# Auto-match dari public/img/
php artisan members:match-photos
```

### Google Sheets seeder gagal (peta)

Pastikan `GOOGLE_SHEETS_API_KEY` valid & Sheet ID public / accessible ke API key tsb.

---

## Resources

- [Laravel 13 Docs](https://laravel.com/docs/13.x)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Chart.js](https://www.chartjs.org/docs/)
- [Leaflet (peta)](https://leafletjs.com/reference.html)
- [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/) — dipakai di seeder Excel-based.

---

## Getting Help

1. Cek log: `tail -f storage/logs/laravel.log`
2. Jalankan `php artisan tinker` untuk introspect model.
3. Pastikan `.env` sudah benar (`APP_KEY`, DB, bot vars).
4. Cek network tab / browser console untuk error frontend.
