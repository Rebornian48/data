# 📦 Panduan Install & Setup Lokal

## Prerequisites

Pastikan sudah terinstall:

- **PHP 8.1+** - <https://www.php.net/>
- **Composer** - <https://getcomposer.org/>
- **Node.js 18+** - <https://nodejs.org/>
- **MySQL/MariaDB** - <https://www.mysql.com/>
- **Git** - <https://git-scm.com/>

---

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/username/jkt48-database.git
cd jkt48-database
```

### 2. Jalankan Setup Script
```bash
# Linux/Mac
chmod +x setup.sh
./setup.sh

# Windows (Git Bash)
bash setup.sh
```

### 3. Manual Setup (Alternatif)
```bash
# Install dependencies
composer install
npm install

# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate

# Jalankan migrations
php artisan migrate

# Seed database (jika ada)
php artisan db:seed

# Build assets
npm run build
```

---

## ⚙️ Konfigurasi Database

### 1. Buat Database
```sql
CREATE DATABASE jkt48_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Edit .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jkt48_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan Migrations
```bash
php artisan migrate
```

### 4. Seed dari Excel (Opsional)
```bash
# Copy file Excel ke database/seeders/data/JKT48_Database.xlsx
php artisan db:seed
```

---

## 🌐 Jalankan Server

```bash
php artisan serve
```

Buka:

- Dashboard: <http://localhost:8000>
- Admin: <http://localhost:8000/admin>

---

## 🔐 Setup Auth (Opsional)

### Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

### Buat Admin User
```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@jkt48.local',
    'password' => bcrypt('password'),
]);
```

---

## 🗄️ Schema SQL (Tanpa Laravel)

Jika hanya butuh database tanpa Laravel:
```bash
mysql -u root -p jkt48_db < schema.sql
```

---

## 📁 File Struktur

```
jkt48-data/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   └── Admin/
│   │       ├── MemberController.php
│   │       ├── SingleController.php
│   │       ├── GenerationController.php
│   │       └── CaptainController.php
│   └── Models/
│       ├── Member.php
│       ├── Generation.php
│       ├── Single.php
│       └── Captain.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/views/
│   ├── layouts/
│   ├── dashboard/
│   └── admin/
├── routes/
│   └── web.php
├── .env.example
├── composer.json
├── package.json
├── schema.sql
└── README.md
```

---

## 🧪 Testing

```bash
# Run PHPUnit tests
php artisan test

# Or with Pest
./vendor/bin/pest
```

---

## 🔧 Common Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:cache

# Create migration
php artisan make:migration create_xxx_table

# Create model
php artisan make:model Xxx -m

# Create controller
php artisan make:controller XxxController --resource

# Tinker (REPL)
php artisan tinker
```

---

## 🐛 Troubleshooting

### Permission Denied
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Database Connection Failed
```bash
# Check .env
cat .env | grep DB_

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

### Class Not Found
```bash
composer dump-autoload
composer install
```

### Token Mismatch
```bash
# Clear session
php artisan session:table
php artisan cache:clear
```

---

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Chart.js](https://www.chartjs.org/docs/)
- [Blade Templates](https://laravel.com/docs/10.x/blade)

---

## 🆘 Getting Help

1. Cek log: `tail -f storage/logs/laravel.log`
2. Jalankan: `php artisan tinker`
3. Cek error di browser console
4. Pastikan `.env` sudah benar
