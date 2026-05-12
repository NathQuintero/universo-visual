#!/bin/sh
# =========================================================================
# Entrypoint para el contenedor en Render.
# Corre las migraciones y arranca el servidor en el puerto que asigne Render.
# =========================================================================
set -e

cd /var/www/html

echo ">> Limpiando cachés previos..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ">> Cacheando configuración para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">> Corriendo migraciones..."
php artisan migrate --force

# Seed solo si la variable SEED_ON_BOOT=true (útil para el primer deploy)
if [ "${SEED_ON_BOOT}" = "true" ]; then
    echo ">> Ejecutando seeders iniciales..."
    php artisan db:seed --force || echo "(seed falló o ya estaba sembrado, continuando)"
fi

PORT="${PORT:-8080}"
echo ">> Arrancando servidor en 0.0.0.0:${PORT}"
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
