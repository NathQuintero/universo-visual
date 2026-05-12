# =========================================================================
# Universo Visual — Imagen para Render (PHP 8.2 + Laravel 12 + Vite 7)
# =========================================================================

# -------- Stage 1: compilar assets de frontend con Vite --------
FROM node:20-alpine AS assets

WORKDIR /app

COPY package*.json vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci && npm run build


# -------- Stage 2: runtime PHP --------
FROM php:8.2-cli-alpine

# Dependencias del sistema + extensiones PHP necesarias:
#   - pdo_pgsql / pgsql      → conexión con PostgreSQL de Render
#   - gd                     → DomPDF (logos, imágenes en recibos)
#   - zip                    → maatwebsite/excel (xlsx)
#   - mbstring, xml, bcmath  → Laravel + Excel
RUN apk add --no-cache \
        libpng-dev libjpeg-turbo-dev freetype-dev \
        libzip-dev zip unzip \
        postgresql-dev \
        oniguruma-dev libxml2-dev \
        git bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql pgsql \
        gd zip bcmath mbstring xml

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cachear dependencias PHP primero (mejor reuso de capa)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Resto del código + assets ya compilados del stage 1
COPY . .
COPY --from=assets /app/public/build ./public/build

# Optimizar autoloader y permisos de Laravel
RUN composer dump-autoload --optimize --no-dev \
    && chmod -R 775 storage bootstrap/cache

# Render inyecta $PORT (típicamente 10000). EXPOSE es informativo.
EXPOSE 8080

# Entrypoint: migra y arranca el servidor
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
