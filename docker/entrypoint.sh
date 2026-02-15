#!/bin/bash
set -e

echo "========================================"
echo "  School Library - Container Starting"
echo "========================================"

# ------------------------------------------
# Wait for MySQL to be ready
# ------------------------------------------
echo "[1/6] Waiting for MySQL..."

MAX_RETRIES=30
RETRY_COUNT=0

while ! php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo 'connected';
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "ERROR: Could not connect to MySQL after $MAX_RETRIES attempts. Exiting."
        exit 1
    fi
    echo "  MySQL not ready yet... retry $RETRY_COUNT/$MAX_RETRIES"
    sleep 2
done

echo "  MySQL is ready!"

# ------------------------------------------
# Run migrations
# ------------------------------------------
echo "[2/6] Running database migrations..."
php artisan migrate --force

# ------------------------------------------
# Cache configuration for performance
# ------------------------------------------
echo "[3/6] Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ------------------------------------------
# Create storage symlink if not exists
# ------------------------------------------
echo "[4/6] Setting up storage link..."
if [ ! -L public/storage ]; then
    php artisan storage:link
    echo "  Storage link created."
else
    echo "  Storage link already exists."
fi

# ------------------------------------------
# Ensure permissions
# ------------------------------------------
echo "[5/6] Setting file permissions..."
chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ------------------------------------------
# Start PHP-FPM
# ------------------------------------------
echo "[6/6] Starting PHP-FPM..."
echo "========================================"
echo "  Application is ready!"
echo "========================================"

exec "$@"
