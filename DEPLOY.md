# 🚀 Panduan Deploy ke Hostinger

## 📋 Persiapan Lokal

### 1. Inisialisasi Git Repository
```bash
cd jkt48_data
git init
git add .
git commit -m "Initial commit: JKT48 Database Laravel App"
```

### 2. Buat Repository di GitHub
1. Login ke https://github.com
2. Click "New repository"
3. Name: `jkt48-database`
4. Pilih **Private** (agar code aman)
5. Click "Create repository"

### 3. Push ke GitHub
```bash
git remote add origin https://github.com/username/jkt48-database.git
git branch -M main
git push -u origin main
```

---

## 🌐 Setup Hosting Hostinger

### 1. Login ke hPanel
1. Buka https://hpanel.hostinger.com
2. Login dengan akun Hostinger kamu

### 2. Setup Domain/Subdomain
1. Go to **Domains** → **Subdomains**
2. Create subdomain:
   - Subdomain: `jkt48`
   - Domain: `rebornian48.my.id`
3. Akan menjadi: `jkt48.rebornian48.my.id`

### 3. Setup Database MySQL
1. Go to **Databases** → **MySQL Databases**
2. Create new database:
   - Database name: `u1234567_jkt48` (akan ada prefix)
   - Username: `u1234567_admin` (akan ada prefix)
   - Password: **Buat password yang kuat**
3. **CATAT** credentials ini:
   - DB Name: `u1234567_jkt48`
   - DB User: `u1234567_admin`
   - DB Password: `password_kamu`
   - DB Host: `localhost`

### 4. Setup Git Deployment (SSH)
1. Go to **Advanced** → **SSH Access**
2. Enable SSH
3. **CATAT** SSH credentials:
   - Host: `jkt48.rebornian48.my.id`
   - Port: `65002` (atau yang tertera)
   - Username: `u1234567`

---

## 🖥️ Deploy via SSH

### 1. Connect to Server via SSH
```bash
# Windows: Gunakan PuTTY atau Windows Terminal
ssh u1234567@jkt48.rebornian48.my.id -p 65002

# Atau gunakan Git Bash
ssh -p 65002 u1234567@jkt48.rebornian48.my.id
```

### 2. Setup Working Directory
```bash
# Buat directory untuk aplikasi
mkdir -p ~/jkt48.rebornian48.my.id
cd ~/jkt48.rebornian48.my.id

# Clone repository
git clone https://github.com/username/jkt48-database.git .

# Install Composer Dependencies
composer install --optimize-autoloader --no-dev
```

### 3. Setup Environment
```bash
# Copy .env.example ke .env
cp .env.example .env

# Generate APP_KEY
php artisan key:generate
```

### 4. Edit .env File
```bash
nano .env
```

Update bagian ini:
```env
APP_NAME="JKT48 Database"
APP_ENV=production
APP_KEY=base64:xxx (otomatis tergenerate)
APP_DEBUG=false
APP_URL=https://jkt48.rebornian48.my.id/data

APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u1234567_jkt48
DB_USERNAME=u1234567_admin
DB_PASSWORD=password_kamu

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 5. Setup Permissions
```bash
# Set permissions untuk storage dan bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 755 public

# Buat symlink untuk storage
php artisan storage:link
```

### 6. Run Migrations & Seed
```bash
# Jalankan migrations
php artisan migrate --force

# Seed database (jika ada)
php artisan db:seed --force
```

### 7. Build Frontend Assets
```bash
# Install npm dependencies
npm install

# Build assets
npm run build
```

### 8. Setup .htaccess untuk Subdirectory
```bash
# Edit public/.htaccess
nano public/.htaccess
```

Pastikan ada:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## ⚙️ Setup Document Root di hPanel

### 1. Go to Websites
1. Login ke hPanel
2. Go to **Websites** → **Manage** (untuk jkt48.rebornian48.my.id)

### 3. Setup Document Root
1. Go to **Advanced** → **Document Root**
2. Set document root ke:
   ```
   /home/u1234567/jkt48.rebornian48.my.id/public
   ```
3. Save

---

## 🔒 Setup SSL Certificate

### 1. Enable SSL
1. Go to **SSL/TLS** di hPanel
2. Enable **Force HTTPS**
3. Install **Let's Encrypt** SSL (gratis)

---

## 🧪 Testing

### 1. Test Aplikasi
Buka browser dan akses:
```
https://jkt48.rebornian48.my.id/data
```

### 2. Test Admin Panel
```
https://jkt48.rebornian48.my.id/data/admin
```

### 3. Buat Admin User
```bash
# Login ke SSH
ssh u1234567@jkt48.rebornian48.my.id -p 65002

# Jalankan artisan tinker
php artisan tinker
```

```php
// Di tinker, jalankan:
User::create([
    'name' => 'Admin',
    'email' => 'admin@jkt48.rebornian48.my.id',
    'password' => bcrypt('password123'),
]);
```

---

## 🔧 Troubleshooting

### Error 500
```bash
# Cek permission
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Cek log
tail -f storage/logs/laravel.log
```

### Database Connection Error
```bash
# Pastikan credentials benar di .env
cat .env | grep DB_

# Test koneksi
php artisan tinker
DB::connection()->getPdo();
```

### Assets Tidak Load
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Route Not Found
```bash
# Pastikan .htaccess ada di public/
cat public/.htaccess

# Rewrite URL
php artisan route:cache
```

---

## 📝 Commands Cheat Sheet

```bash
# SSH
ssh -p 65002 u1234567@jkt48.rebornian48.my.id

# Navigate to app
cd ~/jkt48.rebornian48.my.id

# Pull updates
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Update database
php artisan migrate --force

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check logs
tail -f storage/logs/laravel.log

# Restart queue (jika ada)
php artisan queue:restart
```

---

## 🔄 Update Aplikasi

### Via SSH
```bash
cd ~/jkt48.rebornian48.my.id
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan cache:clear
npm run build
```

### Via Git Push (Auto Deploy)
Jika ingin auto-deploy saat push ke GitHub:
1. Go to hPanel → **Git**
2. Add repository
3. Set branch: `main`
4. Set deployment path: `/home/u1234567/jkt48.rebornian48.my.id`
5. Enable auto-deploy

---

## 📊 Database Backup

### Manual Backup via SSH
```bash
# Backup database
mysqldump -u u1234567_admin -p u1234567_jkt48 > backup_$(date +%Y%m%d).sql
```

### Auto Backup di hPanel
1. Go to **Databases** → **Backups**
2. Enable auto backup
3. Set schedule (daily/weekly)

---

## 🎯 URL Structure

| URL | Description |
|-----|-------------|
| `https://jkt48.rebornian48.my.id/data` | Dashboard Publik |
| `https://jkt48.rebornian48.my.id/data/members` | Daftar Member |
| `https://jkt48.rebornian48.my.id/data/members/{id}` | Detail Member |
| `https://jkt48.rebornian48.my.id/data/admin` | Admin Panel |
| `https://jkt48.rebornian48.my.id/data/admin/members` | Kelola Member |

---

## ⚠️ Important Notes

1. **Jangan commit `.env` ke Git** - sudah ada di `.gitignore`
2. **Backup database secara berkala**
3. **Gunakan password yang kuat** untuk database dan admin
4. **Enable SSL** untuk keamanan
5. **Monitor log** jika ada error

---

## 🆘 Support

Jika ada masalah:
1. Cek log: `tail -f storage/logs/laravel.log`
2. Cek error di browser console
3. Pastikan permissions benar
4. Restart Apache/Nginx di hPanel