#!/bin/bash

# Matikan script jika ada error
set -e

echo "🚀 Memulai proses deployment..."

# 1. Masuk ke mode maintenance (opsional, agar user tidak melihat error saat update)
echo "🚧 Mengaktifkan mode maintenance..."
php artisan down || true

# 2. Ambil perubahan terbaru dari Git
echo "📥 Menarik kode terbaru dari repository..."
git pull origin main

# 3. Instalasi dependency PHP (Backend)
echo "📦 Mengupdate library Composer..."
composer install --no-dev --optimize-autoloader

# 4. Sinkronisasi Database
echo "🗄️ Menjalankan migrasi database..."
php artisan migrate --force

# 5. Instalasi & Build Frontend
echo "🎨 Membangun aset frontend (Vite)..."
npm install
npm run build

# 6. Pengaturan Permission (Krusial untuk kenyamanan di server)
echo "🔐 Mengatur ulang hak akses dan kepemilikan file..."
# Pastikan owner adalah user saat ini dan group adalah www-data
sudo chown -R $USER:www-data .

# Folder yang wajib bisa ditulis oleh web server
sudo chmod -R 775 storage bootstrap/cache database public/build
# Khusus file database SQLite
if [ -f "database/database.sqlite" ]; then
    sudo chmod 664 database/database.sqlite
fi

# 7. Optimasi Cache Laravel
echo "⚡ Mengoptimalkan cache aplikasi..."
php artisan optimize

# 8. Matikan mode maintenance
echo "✅ Mengaktifkan kembali aplikasi..."
php artisan up

echo "✨ Deployment berhasil diselesaikan!"
