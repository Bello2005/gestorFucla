# ─── Stage 1: Node – build Vite assets ──────────────────────────────────────
FROM node:22-alpine AS node-build

WORKDIR /app

COPY package*.json ./
RUN npm ci --prefer-offline

COPY resources/ resources/
COPY vite.config.js ./
COPY public/ public/

RUN npm run build

# ─── Stage 2: PHP runtime ────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS php-base

# System deps + PHP extensions
RUN apk add --no-cache \
        nginx supervisor curl unzip \
        libpng-dev libjpeg-turbo-dev freetype-dev \
        libzip-dev icu-dev libxml2-dev \
        oniguruma-dev \
        postgresql16-client postgresql16-dev \
    && docker-php-ext-configure gd \
          --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
          pdo_pgsql pgsql \
          gd zip bcmath mbstring xml intl exif opcache \
    && rm -rf /var/cache/apk/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# PHP dependencies (no dev, optimised autoloader)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Application source
COPY . .

# Built JS/CSS assets from Stage 1
COPY --from=node-build /app/public/build public/build

# Permissions
RUN mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
