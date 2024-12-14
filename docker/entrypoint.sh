#!/bin/bash

# Exit on error
set -e

echo "🚀 Initializing Laravel application..."


# Copy .env file if not exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d "vendor" ]; then
    composer install --no-interaction --no-plugins --no-scripts
fi

if [ ! -f .env ] || ! grep -q "^APP_KEY=" .env || grep -q "^APP_KEY=base64:$" .env; then
    php artisan key:generate --force
fi

if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

#php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www:www storage
chown -R www:www bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache


php-fpm

# Execute the original CMD (e.g. php-fpm)
#exec "$@"
