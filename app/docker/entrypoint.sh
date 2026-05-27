#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
  cp .env.example .env
fi

mkdir -p storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --no-progress
fi

if [ -f vendor/autoload.php ] && ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"
