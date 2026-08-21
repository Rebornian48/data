# JKT48 Database - Laravel Application

Aplikasi web lengkap untuk mengelola database JKT48 (member, generasi, single, dan riwayat kapten) dengan dashboard publik dan panel admin.

## Struktur File

```
jkt48-app/
├── schema.sql                          # Raw SQL schema (untuk setup manual)
├── database/
│   ├── migrations/                     # Laravel migrations
│   │   ├── ..._create_generations_table.php
│   │   ├── ..._create_singles_table.php
│   │   ├── ..._create_members_table.php
│   │   ├── ..._create_member_singles_table.php
│   │   └── ..._create_captains_table.php
│   └── seeders/
│       └── DatabaseSeeder.php          # Import dari Excel
├── app/
│   ├── Models/
│   │   ├── Generation.php
│   │   ├── Single.php
│   │   ├── Member.php
│   │   └── Captain.php
│   └── Http/Controllers/
│       ├── DashboardController.php     # Public dashboard
│       ├── Controller.php
│       └── Admin/
│           ├── MemberController.php
│           ├── SingleController.php
│           ├── GenerationController.php
│           └── CaptainController.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php               # Layout dashboard publik
│   │   └── admin.blade.php             # Layout admin dengan sidebar
│   ├── dashboard/
│   │   ├── index.blade.php             # Main dashboard (stats + chart)
│   │   ├── members.blade.php           # Public members list
│   │   └── member.blade.php            # Public member detail
│   └── admin/
│       ├── members/                    # Admin CRUD members
│       ├── singles/                    # Admin CRUD singles
│       ├── generations/                # Admin CRUD generations
│       └── captains/                   # Admin CRUD captains
└── routes/
    └── web.php
```

## Setup Instructions

### 1. Buat proyek Laravel baru

```bash
composer create-project laravel/laravel jkt48-app
cd jkt48-app
```

### 2. Copy file dari paket ini

Copy folder `app/`, `database/`, `resources/`, dan `routes/` ke proyek Laravel-mu (overwrite yang ada).

### 3. Install dependency untuk Excel seeder

```bash
composer require phpoffice/phpspreadsheet
```

### 4. Setup database (`.env`)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jkt48_db
DB_USERNAME=root
DB_PASSWORD=
```

Buat database dulu:
```sql
CREATE DATABASE jkt48_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan migration

```bash
php artisan migrate
```

### 6. Seed dari Excel

Copy file Excel ke `database/seeders/data/JKT48_Database.xlsx`, lalu:

```bash
php artisan db:seed
```

### 7. (Opsional) Setup auth

Route admin dilindungi middleware `auth`. Install starter kit:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

Buat user admin lewat `php artisan tinker`:

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@jkt48.local',
    'password' => bcrypt('password'),
]);
```

### 8. Jalankan server

```bash
php artisan serve
```

Buka `http://localhost:8000` untuk dashboard publik dan `http://localhost:8000/admin` untuk panel admin (login dulu).

## Setup Alternatif (Tanpa Laravel)

Kalau cuma butuh database saja, jalankan `schema.sql` langsung di MySQL/MariaDB:

```bash
mysql -u root -p jkt48_db < schema.sql
```

File ini sudah berisi view `v_member_stats` dan `v_generation_summary` yang siap dipakai untuk reporting.

## Fitur Utama

### Dashboard Publik (`/`)
- Statistik total member, aktif, lulus, generasi, single
- Chart member per generasi (stacked bar: aktif vs lulus)
- Chart distribusi usia (donut)
- Kapten yang sedang menjabat
- Top 10 tenure terlama
- Top 10 senbatsu terbanyak
- Top center
- Top 10 kota kelahiran (progress bar)

### Halaman Member Publik (`/members`)
- Filter berdasarkan nama, generasi, status
- Grid cards dengan foto/inisial
- Pagination

### Detail Member (`/members/{id}`)
- Profil lengkap
- Timeline karier visual (masuk, promosi, umumkan lulus, lulus)
- Daftar partisipasi single (dengan label center)
- Riwayat kapten
- Statistik: tahun aktif, jumlah senbatsu, center

### Admin Panel (`/admin`)
- **Members**: CRUD lengkap dengan form 2-kolom (identitas + karier + kelulusan), pilih partisipasi single per member dengan role member/center
- **Generasi**: CRUD dengan info tanggal masuk, deskripsi
- **Single**: CRUD dengan info judul, kode, tanggal rilis, jumlah senbatsu & center
- **Kapten**: CRUD riwayat kapten dengan durasi otomatis

## Model Relationships

```php
// Member belongs to Generation
$member->generation;

// Member has many Singles (many-to-many)
$member->singles;
$member->centerSingles;

// Member has many Captain records
$member->captains;

// Generation has many Members
$generation->members;
$generation->activeMembers;
$generation->graduatedMembers;

// Single has many Members (many-to-many)
$single->members;
$single->centers;

// Captain belongs to Member
$captain->member;
```

## Computed Attributes

Model `Member` punya accessor otomatis:
- `$member->current_age` — usia saat ini (atau saat lulus)
- `$member->age_at_join` — usia saat masuk
- `$member->days_in_jkt48` — total hari sebagai member
- `$member->years_in_jkt48` — total tahun (rounded 1 desimal)
- `$member->totalSenbatsu()` — jumlah single yang diikuti
- `$member->totalCenter()` — jumlah center

## Scopes

```php
// Members
Member::active()->get();                       // Semua yang aktif
Member::graduated()->get();                    // Semua yang lulus
Member::search('freya')->get();                // Cari nama/panggilan/kota
Member::byGeneration($genId)->get();           // Filter by generation

// Captains
Captain::active()->get();                      // Yang masih menjabat
Captain::position('Kapten JKT48')->get();      // Filter posisi
```

## Styling

Semua view pakai **Tailwind CSS via CDN** (untuk kemudahan setup), dengan font Inter. Brand color JKT48 merah `#E60012`. Chart pakai **Chart.js**. Kalau mau produksi, sebaiknya install Tailwind lokal via `npm`.
