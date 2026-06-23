#!/bin/bash
set -e

# Entrypoint — successProjectBase Laravel Container
# Handles: wait-for-db, composer, migrations, storage, permissions, services

echo "=============================================="
echo "  Starting Success Project Container..."
echo "=============================================="
────────────────────────────────────────────
echo "Waiting for MariaDB..."
MAX_RETRIES=60
RETRY_COUNT=0
until php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'ok'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "❌ MariaDB is not available after ${MAX_RETRIES} retries. Exiting."
        exit 1
    fi
    echo "   Attempt ${RETRY_COUNT}/${MAX_RETRIES}..."
    sleep 3
done
echo "✅ MariaDB is ready!"

# ── Install Composer dependencies (if needed) ───────────────────────────────
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo " Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    echo " Composer dependencies already installed."
fi

# ── Generate APP_KEY if missing ──────────────────────────────────────────────
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo " Generating application key..."
    php artisan key:generate --force
fi

# ── Storage symlink ─────────────────────────────────────────────────────────
if [ ! -L "public/storage" ]; then
    echo " Creating storage symlink..."
    php artisan storage:link
fi

# ── Fix permissions ─────────────────────────────────────────────────────────
echo " Setting directory permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ── Run migrations ──────────────────────────────────────────────────────────
echo " Running migrations..."
php artisan migrate --force 2>/dev/null || echo " Migrations skipped (dump may have already created tables)"

echo "Optimizing Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "  ✅ Container ready!"
echo "  🌐 App: http://localhost:8001"

exec "$@"
