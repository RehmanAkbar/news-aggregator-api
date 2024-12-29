#!/bin/bash

set -e

echo "🚀 Starting entrypoint script..."

# Function to test MySQL connection
wait_for_mysql() {
    echo "⏳ Waiting for MySQL..."
    for i in {1..30}; do
        if php -r "try { \$pdo = new PDO('mysql:host=db;', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'connected'; } catch (PDOException \$e) { exit(1); };" 2>/dev/null; then
            echo "✅ MySQL is ready!"
            return 0
        fi
        echo "MySQL not ready... waiting"
        sleep 2
    done
    echo "❌ MySQL connection timeout"
    exit 1
}

# Wait for MySQL
wait_for_mysql

# Generate application key if not set
if [ ! -f /var/www/html/.env ]; then
    cp .env.example .env
fi

php artisan key:generate --force

# Run migrations and seeders
echo "⏳ Running database migrations..."
php artisan migrate --force
echo "✅ Migrations completed!"

echo "⏳ Running database seeders..."
# php artisan db:seed --force
echo "✅ Seeders completed!"

# Storage link (only if it doesn't exist)
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Cache configuration and routes
echo "⏳ Optimizing Laravel..."
php artisan optimize
echo "✅ Laravel optimization completed!"

# Clear existing pid and socket if they exist
rm -f /tmp/supervisor.sock
rm -f /var/run/supervisord.pid

echo "🎉 Starting supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf