FROM php:8.2-fpm-bookworm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# ─── OPcache: tăng tốc PHP tối đa ────────────────────────────────────────────
# validate_timestamps=0 → không stat() filesystem mỗi request (quan trọng nhất
#   khi chạy trên WSL bind-mount, tránh cross-filesystem I/O cực chậm)
# Sau khi sửa code PHP, chỉ cần: docker-compose restart app
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_wasted_percentage=5'; \
    echo 'opcache.interned_strings_buffer=32'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.jit=tracing'; \
    echo 'opcache.jit_buffer_size=128M'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# ─── PHP performance tuning ───────────────────────────────────────────────────
RUN { \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=600'; \
} > /usr/local/etc/php/conf.d/performance.ini

# ─── PHP-FPM pool: file riêng để không conflict www.conf ─────────────────────
# dynamic: spawn workers theo nhu cầu, tiết kiệm RAM khi ít request
RUN { \
    echo '[www]'; \
    echo 'pm = dynamic'; \
    echo 'pm.max_children = 20'; \
    echo 'pm.start_servers = 5'; \
    echo 'pm.min_spare_servers = 3'; \
    echo 'pm.max_spare_servers = 8'; \
    echo 'pm.max_requests = 500'; \
    echo 'pm.process_idle_timeout = 10s'; \
} > /usr/local/etc/php-fpm.d/zz-performance.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy và cấp quyền entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

CMD ["sh", "/usr/local/bin/entrypoint.sh"]