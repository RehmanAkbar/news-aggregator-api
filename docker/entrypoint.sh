#!/bin/bash

# Exit on error
set -e

echo "🚀 Initializing Laravel application..."

cd /var/www

echo "Working directory: $(pwd)"

chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache


exec "$@"
