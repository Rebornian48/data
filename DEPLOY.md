# Panduan Deploy ke Hostinger

Deploy target: `https://jkt48.rebornian48.my.id` (root domain, bukan subdirektori).
Metode: Git auto-deploy dari `main` → `~/domains/jkt48.rebornian48.my.id/public_html`.

---

## Persiapan Lokal

### 1. Pastikan repo push ke GitHub

Repo utama: <https://github.com/Rebornian48/data>.

```bash
git push origin main
```

Auto-deploy Hostinger akan pull dari `main` setiap push berikutnya.

---

## Setup Hostinger (One-Time)

### 1. Subdomain

hPanel → **Domains → Subdomains**:

- Subdomain: `jkt48`
- Domain: `rebornian48.my.id`
- Hasil: `jkt48.rebornian48.my.id`

### 2. Database MySQL

hPanel → **Databases → MySQL Databases** — buat DB baru. Catat:

- DB Name: `u1234567_jkt48`
- DB User: `u1234567_admin`
- DB Password: **password kuat**
- DB Host: `localhost`

### 3. Enable SSH

hPanel → **Advanced → SSH Access** — enable. Catat:

- Host: `<SSH_HOST>` (biasanya berbeda dari domain — ada di hPanel)
- Port: `65002`
- User: `u1234567`

### 4. Git Auto-Deploy

hPanel → **Advanced → Git**:

- Repository URL: `https://github.com/Rebornian48/data.git`
- Branch: `main`
- Deployment path: `/home/u1234567/domains/jkt48.rebornian48.my.id/public_html`
- Enable **Deploy otomatis** (webhook GitHub).

Klik **Deploy** pertama kali untuk clone repo.

### 5. Document Root

hPanel → **Websites → Manage** (`jkt48.rebornian48.my.id`) → **Advanced → Document Root**:

```
/home/u1234567/domains/jkt48.rebornian48.my.id/public_html/public
```

Wajib nunjuk ke folder `public/` (Laravel front-controller).

### 6. SSL

hPanel → **SSL/TLS** → install Let's Encrypt → **Force HTTPS**.

---

## First-Time Server Setup (via SSH)

```bash
ssh -p 65002 u1234567@<SSH_HOST>
cd ~/domains/jkt48.rebornian48.my.id/public_html
```

### 1. Composer

Composer sudah tersedia di Hostinger. Kalau tidak:

```bash
composer install --optimize-autoloader --no-dev
```

> `composer.lock` sudah di-commit — deploy reproducible.

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate --force
nano .env
```

Isi minimal:

```env
APP_NAME="JKT48 Database"
APP_ENV=production
APP_KEY=base64:...          # auto dari key:generate
APP_DEBUG=false
APP_URL=https://jkt48.rebornian48.my.id
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u1234567_jkt48
DB_USERNAME=u1234567_admin
DB_PASSWORD=<password_db>

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Bot notifikasi (opsional)
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=...
TELEGRAM_CHAT_IDS=...
TELEGRAM_WEBHOOK_SECRET=random_string

DISCORD_ENABLED=true
DISCORD_WEBHOOK_URLS=https://discord.com/api/webhooks/...
DISCORD_PUBLIC_KEY=...        # kalau pakai slash command

NOTIFICATIONS_DAILY_TIME=08:00

# Peta (opsional — cuma buat seeder JKT48MapSeeder)
GOOGLE_SHEETS_API_KEY=...
JKT48_MAP_SHEET_ID=1FinIC52jFCi5fL7oN5qZ-BKocZvBfxbiCTrzXetnANo
```

### 3. Permissions

```bash
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
chmod -R 755 public
```

### 4. Migrate + Seed

```bash
php artisan migrate --seed --force
php artisan config:clear
```

Seeder bikin admin default (`admin` / `data_jkt48`). Ganti password segera via `/admin/password`.

Untuk re-run seeder tertentu setelah update data (tanpa `migrate:fresh`):

```bash
php artisan db:seed --class=SingleSeeder --force
php artisan db:seed --class=MemberSingleSeeder --force
php artisan db:seed --class=DiskografiSeeder --force
```

`MemberSingleSeeder` idempotent per single — aman dijalankan ulang; pivot lama tiap single dihapus lalu di-insert lagi sesuai data terbaru.

`DiskografiSeeder` juga idempotent — baca ulang `database/data/diskografi/*.json` dan sinkronkan `songs`, `albums`, `setlists`, `coupling_songs`, `sub_units`, `mv_locations`. Aman di-rerun setelah update JSON tanpa `migrate:fresh`.

### 5. Cron Scheduler (Bot Notifikasi)

hPanel → **Advanced → Cron Jobs** → tambah:

```
* * * * * cd ~/domains/jkt48.rebornian48.my.id/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Cron 1 menit sekali. Laravel scheduler internal-nya yg atur `notifications:daily` jalan sekali di jam `NOTIFICATIONS_DAILY_TIME` (default 08:00 Asia/Jakarta).

### 6. Set Telegram Webhook (kalau pakai Telegram bot)

```bash
curl "https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/setWebhook?url=https://jkt48.rebornian48.my.id/webhooks/telegram/<TELEGRAM_WEBHOOK_SECRET>"
```

Cek:

```bash
curl "https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/getWebhookInfo"
```

### 7. Set Discord Interactions Endpoint (kalau pakai slash command)

Developer Portal → aplikasi Discord → **Interactions Endpoint URL**:

```
https://jkt48.rebornian48.my.id/webhooks/discord
```

Discord akan tes signature Ed25519 — pastikan `DISCORD_PUBLIC_KEY` di `.env` sudah benar. Register command (`/ultah`, `/lulus`, `/member`, `/jadwal`, `/help`) via HTTP API Discord.

---

## Testing Post-Deploy

```
https://jkt48.rebornian48.my.id                       # dashboard publik
https://jkt48.rebornian48.my.id/members               # daftar member
https://jkt48.rebornian48.my.id/singles               # daftar single (+ coupling)
https://jkt48.rebornian48.my.id/albums                # album + EP
https://jkt48.rebornian48.my.id/setlists              # setlist reguler + special
https://jkt48.rebornian48.my.id/statistik             # statistik per generasi
https://jkt48.rebornian48.my.id/kalender              # kalender event
https://jkt48.rebornian48.my.id/peta                  # peta interaktif
https://jkt48.rebornian48.my.id/sorter/member         # member sorter
https://jkt48.rebornian48.my.id/sorter/song           # song sorter
https://jkt48.rebornian48.my.id/admin                 # panel admin
https://jkt48.rebornian48.my.id/api/docs              # Swagger UI
https://jkt48.rebornian48.my.id/api/v1/members        # REST API v1 (core)
https://jkt48.rebornian48.my.id/api/v1/songs          # REST API v1 (diskografi)
```

---

## Troubleshooting

### Error 500

```bash
tail -f storage/logs/laravel.log
chmod -R 775 storage bootstrap/cache
```

### Database connection error

```bash
cat .env | grep DB_
php artisan tinker
>>> DB::connection()->getPdo();
```

### Assets / CSS tidak load

Frontend pakai Tailwind CDN — cek `public/css/neo.css` masih ada. `chmod -R 755 public` bila permission salah.

### Route not found (404 di semua path)

Pastikan document root nunjuk ke `.../public_html/public` (bukan `.../public_html`). Cek `public/.htaccess` ada.

### Bot Telegram tidak reply

```bash
# Cek webhook terpasang
curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo"

# Cek log Laravel
tail -f storage/logs/laravel.log
```

### Broadcast harian tidak jalan

```bash
# Test manual dulu
php artisan notifications:daily --date=2026-09-15

# Cek cron scheduler kepasang
crontab -l

# Cek log
tail -f storage/logs/laravel.log
```

---

## Update Aplikasi

### Via Git Auto-Deploy

Cukup push ke `main` — Hostinger pull otomatis.

Kalau ada migration baru, SSH sekali:

```bash
cd ~/domains/jkt48.rebornian48.my.id/public_html
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

### Via SSH Manual

```bash
cd ~/domains/jkt48.rebornian48.my.id/public_html
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

---

## Database Backup

### Manual via SSH

```bash
mysqldump -u u1234567_admin -p u1234567_jkt48 > backup_$(date +%Y%m%d).sql
```

### Auto di hPanel

**Databases → Backups** → enable auto backup (daily/weekly).

---

## URL Structure

| URL | Deskripsi |
|---|---|
| `/` | Dashboard publik |
| `/members`, `/members/{id}` | Daftar & detail member |
| `/singles`, `/singles/{id}` | Singles + detail (cover, senbatsu, daftar lagu, coupling) |
| `/albums`, `/albums/{id}` | Album + EP dgn tracklist |
| `/setlists`, `/setlists/{id}` | Setlist reguler + special |
| `/captains` | Captains |
| `/statistik`, `/restrukturisasi` | Ringkasan statistik |
| `/kalender` | Kalender event (ultah, announce, lulus) |
| `/peta`, `/peta/{slug}` | Peta interaktif |
| `/sorter`, `/sorter/{type}` | Sorter merge sort (types: `member`, `song`) |
| `/api/docs` | Swagger UI |
| `/api/v1/*` | REST API v1 |
| `/webhooks/telegram/{secret}` | Telegram inbound (POST) |
| `/webhooks/discord` | Discord Interactions (POST) |
| `/admin` | Panel admin (login required) |
| `/admin/{members,singles,generations,captains,teams,maps}` | CRUD core |
| `/admin/{songs,albums,setlists,coupling-songs,sub-units,mv-locations}` | CRUD diskografi |
| `/admin/docs/{api,telegram,discord}` | Dokumentasi in-app |
| `/admin/password` | Ganti password admin |

---

## Important Notes

1. **Jangan commit `.env`** — sudah di `.gitignore`.
2. **Backup DB berkala** — data member/generasi tidak trivial diseed ulang.
3. **Ganti password admin default** (`data_jkt48`) segera via `/admin/password`.
4. **Enable Force HTTPS** — Discord Interactions Endpoint wajib HTTPS.
5. **Monitor log** — `storage/logs/laravel.log` untuk error runtime.

---

## Support

1. Cek log: `tail -f storage/logs/laravel.log`
2. Cek browser console untuk error frontend.
3. Cek document root & permissions.
4. Restart web server via hPanel bila perlu.
