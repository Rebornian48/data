# JKT48 Database - Setup Script (PowerShell)
# Run this on Windows

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  JKT48 Database - Setup Script" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Check if git is installed
if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Git tidak terinstall. Install git terlebih dahulu." -ForegroundColor Red
    exit 1
}

# Check if composer is installed
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Composer tidak terinstall. Install composer terlebih dahulu." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Git terinstall" -ForegroundColor Green
Write-Host "✅ Composer terinstall" -ForegroundColor Green
Write-Host ""

# Initialize git repository
Write-Host "📁 Inisialisasi Git repository..." -ForegroundColor Yellow
if (-not (Test-Path ".git")) {
    git init
    Write-Host "✅ Git repository berhasil diinisialisasi" -ForegroundColor Green
} else {
    Write-Host "✅ Git repository sudah ada" -ForegroundColor Green
}
Write-Host ""

# Create .env file
Write-Host "🔧 Membuat file .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Copy-Item .env.example .env
    Write-Host "✅ File .env berhasil dibuat dari .env.example" -ForegroundColor Green
    Write-Host "⚠️  Jangan lupa update .env dengan credentials yang benar!" -ForegroundColor Yellow
} else {
    Write-Host "✅ File .env sudah ada" -ForegroundColor Green
}
Write-Host ""

# Install composer dependencies
Write-Host "📦 Install Composer dependencies..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Composer dependencies berhasil diinstall" -ForegroundColor Green
} else {
    Write-Host "❌ Gagal install composer dependencies" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Install npm dependencies
Write-Host "📦 Install npm dependencies..." -ForegroundColor Yellow
if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm install
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ npm dependencies berhasil diinstall" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Gagal install npm dependencies, tapi lanjut..." -ForegroundColor Yellow
    }
} else {
    Write-Host "⚠️  npm tidak terinstall, skip install npm dependencies" -ForegroundColor Yellow
}
Write-Host ""

# Generate application key
Write-Host "🔑 Generate application key..." -ForegroundColor Yellow
php artisan key:generate
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Application key berhasil di-generate" -ForegroundColor Green
} else {
    Write-Host "❌ Gagal generate application key" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Run migrations (optional)
$runMigrate = Read-Host "🚀 Jalankan migrations sekarang? (y/n)"
if ($runMigrate -eq "y") {
    Write-Host "🗄️  Running migrations..." -ForegroundColor Yellow
    php artisan migrate
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Migrations berhasil dijalankan" -ForegroundColor Green
    } else {
        Write-Host "❌ Gagal menjalankan migrations" -ForegroundColor Red
        Write-Host "⚠️  Pastikan database sudah dikonfigurasi di .env" -ForegroundColor Yellow
    }
}
Write-Host ""

# Build assets
$buildAssets = Read-Host "🎨 Build frontend assets? (y/n)"
if ($buildAssets -eq "y") {
    Write-Host "🎨 Building assets..." -ForegroundColor Yellow
    npm run build
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Assets berhasil di-build" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Gagal build assets, tapi lanjut..." -ForegroundColor Yellow
    }
}
Write-Host ""

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  ✅ Setup selesai!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor White
Write-Host "1. Update file .env dengan credentials database" -ForegroundColor White
Write-Host "2. Buat repository di GitHub" -ForegroundColor White
Write-Host "3. Jalankan command berikut untuk push ke GitHub:" -ForegroundColor White
Write-Host ""
Write-Host "   git add ." -ForegroundColor Yellow
Write-Host '   git commit -m "Initial commit"' -ForegroundColor Yellow
Write-Host "   git remote add origin https://github.com/username/repo.git" -ForegroundColor Yellow
Write-Host "   git branch -M main" -ForegroundColor Yellow
Write-Host "   git push -u origin main" -ForegroundColor Yellow
Write-Host ""
Write-Host "4. Ikuti panduan di DEPLOY.md untuk deploy ke Hostinger" -ForegroundColor White
Write-Host ""
Write-Host "Selamat coding! 🎵" -ForegroundColor Green