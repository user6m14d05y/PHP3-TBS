#!/bin/sh
set -e

cd /var/www/html

# Cài composer dependencies nếu chưa có vendor
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Tạo symlink storage nếu chưa có
if [ ! -L "public/storage" ]; then
    php artisan storage:link --quiet 2>/dev/null || true
fi

# Refresh & build Laravel caches để giảm boot time khi dùng Docker bind-mount
# - optimize:clear: xóa cache cũ (tránh stale cache)
# - config:cache:  gộp toàn bộ config vào 1 file → không parse .env mỗi request
# - route:cache:   compile routes → không parse api.php mỗi request
# - view:cache:    pre-compile Blade templates → không compile khi request đến
# - event:cache:   compile event listeners
echo "Building Laravel caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "All caches built! Starting PHP-FPM..."

# Khởi chạy PHP-FPM (foreground)
exec php-fpm
