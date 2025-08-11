#!/bin/sh
set -e

echo "Running database migrations..."
php artisan migrate --force

exec "$@"