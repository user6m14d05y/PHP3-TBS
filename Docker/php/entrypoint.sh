#!/bin/sh
set -e

cd /var/www/html

# Cài composer dependencies nếu chưa có vendor
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Chạy Laravel optimization commands
echo "Optimizing Laravel..."
php artisan config:cache    # Gộp 12+ config files thành 1 file duy nhất
php artisan route:cache     # Compile routes thành cached file
php artisan view:cache      # Pre-compile tất cả Blade views

echo "Laravel optimized! Starting PHP-FPM..."

# Khởi chạy PHP-FPM
exec php-fpm
