#!/bin/bash

# JKT48 Database - Setup Script
# Run this on your local machine before first commit

echo "=========================================="
echo "  JKT48 Database - Setup Script"
echo "=========================================="
echo ""

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git tidak terinstall. Install git terlebih dahulu."
    exit 1
fi

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer tidak terinstall. Install composer terlebih dahulu."
    exit 1
fi

echo "✅ Git terinstall"
echo "✅ Composer terinstall"
echo ""

# Initialize git repository
echo "📁 Inisialisasi Git repository..."
if [ ! -d ".git" ]; then
    git init
    echo "✅ Git repository berhasil diinisialisasi"
else
    echo "✅ Git repository sudah ada"
fi
echo ""

# Create .env file
echo "🔧 Membuat file .env..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✅ File .env berhasil dibuat dari .env.example"
    echo "⚠️  Jangan lupa update .env dengan credentials yang benar!"
else
    echo "✅ File .env sudah ada"
fi
echo ""

# Install composer dependencies
echo "📦 Install Composer dependencies..."
composer install
if [ $? -eq 0 ]; then
    echo "✅ Composer dependencies berhasil diinstall"
else
    echo "❌ Gagal install composer dependencies"
    exit 1
fi
echo ""

# Install npm dependencies
echo "📦 Install npm dependencies..."
if command -v npm &> /dev/null; then
    npm install
    if [ $? -eq 0 ]; then
        echo "✅ npm dependencies berhasil diinstall"
    else
        echo "⚠️  Gagal install npm dependencies, tapi lanjut..."
    fi
else
    echo "⚠️  npm tidak terinstall, skip install npm dependencies"
fi
echo ""

# Generate application key
echo "🔑 Generate application key..."
php artisan key:generate
if [ $? -eq 0 ]; then
    echo "✅ Application key berhasil di-generate"
else
    echo "❌ Gagal generate application key"
    exit 1
fi
echo ""

# Set permissions
echo "🔒 Set permissions..."
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    chmod -R 755 public
    echo "✅ Permissions berhasil di-set"
else
    echo "⚠️  Skip set permissions (bukan Linux)"
fi
echo ""

# Run migrations (optional)
read -p "🚀 Jalankan migrations sekarang? (y/n): " run_migrate
if [ "$run_migrate" = "y" ]; then
    echo "🗄️  Running migrations..."
    php artisan migrate
    if [ $? -eq 0 ]; then
        echo "✅ Migrations berhasil dijalankan"
    else
        echo "❌ Gagal menjalankan migrations"
        echo "⚠️  Pastikan database sudah dikonfigurasi di .env"
    fi
fi
echo ""

# Build assets
read -p "🎨 Build frontend assets? (y/n): " build_assets
if [ "$build_assets" = "y" ]; then
    echo "🎨 Building assets..."
    npm run build
    if [ $? -eq 0 ]; then
        echo "✅ Assets berhasil di-build"
    else
        echo "⚠️  Gagal build assets, tapi lanjut..."
    fi
fi
echo ""

echo "=========================================="
echo "  ✅ Setup selesai!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Update file .env dengan credentials database"
echo "2. Buat repository di GitHub"
echo "3. Jalankan command berikut untuk push ke GitHub:"
echo ""
echo "   git add ."
echo "   git commit -m 'Initial commit'"
echo "   git remote add origin https://github.com/username/repo.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""
echo "4. Ikuti panduan di DEPLOY.md untuk deploy ke Hostinger"
echo ""
echo "Selamat coding! 🎵"