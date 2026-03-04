#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ -w storage ] && [ -w bootstrap/cache ]; then
  chown -R www-data:www-data storage bootstrap/cache || true
fi

exec "$@"
