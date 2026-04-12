#!/bin/sh
set -e

cd /var/www/html

# ── Ensure writable directories exist (idempotent) ─────────────────────────
mkdir -p storage/logs \
         storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache \
         bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── Build .env from environment variables ──────────────────────────────────
# Strip surrounding quotes from APP_NAME in case Render wraps the value
_APP_NAME="${APP_NAME:-Sistema de Gestion}"
_APP_NAME=$(printf '%s' "$_APP_NAME" | sed 's/^"//;s/"$//')

cat > .env <<EOF
APP_NAME="$_APP_NAME"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DB_SSLMODE=${DB_SSLMODE:-require}

SESSION_DRIVER=${SESSION_DRIVER:-cookie}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=${SESSION_DOMAIN:-null}

CACHE_DRIVER=${CACHE_DRIVER:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}

BROADCAST_CONNECTION=log
EOF

# ── APP_KEY: generate if not provided ──────────────────────────────────────
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force
fi

# ── Optimize for production ────────────────────────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache || true

# ── Run migrations ─────────────────────────────────────────────────────────
php artisan migrate --force

# ── Storage link ──────────────────────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ── Start services ─────────────────────────────────────────────────────────
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
