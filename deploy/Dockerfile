# =============================================================
# Micrositio OICM — Dockerfile de despliegue
# =============================================================
# Construcción en dos etapas:
#   Stage 1 (assets):  Node.js 22 compila JS/CSS de Vite (Tailwind, Alpine…)
#   Stage 2 (app):     PHP 8.4-FPM + Nginx + Supervisor
#
# Base de datos: MySQL (pdo_mysql). SÍ maneja subida de archivos (documentos,
# imágenes de noticias, galería) — ver deploy/php/php.ini y nginx/default.conf.
# PHP 8.4 satisface el composer.json (^8.3); se puede subir a 8.5 cuando la
# imagen oficial php:8.5-fpm sea estable.
# =============================================================

# -------------------------------------------------------------
# Stage 1: Compilar assets con Node.js
# -------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --prefer-offline --no-audit

# Recursos que necesita Vite para compilar (incluye @fontsource self-hosted).
COPY resources/ resources/
COPY public/ public/
COPY vite.config.js ./

RUN npm run build


# -------------------------------------------------------------
# Stage 2: Imagen de despliegue PHP 8.4
# -------------------------------------------------------------
FROM php:8.4-fpm-bookworm AS app

# nginx: servidor web · supervisor: gestiona nginx + php-fpm
# Las librerías lib* son requeridas por las extensiones PHP (gd para el
# redimensionado de imágenes de noticias/galería).
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        git \
        curl \
        zip \
        unzip \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP requeridas por Laravel 13 + MySQL:
# - pdo_mysql: conexión a MySQL 8
# - mbstring, xml, bcmath: Laravel core
# - zip:  composer / manejo de archivos
# - intl: internacionalización (fechas en español)
# - gd:   redimensionado de imágenes (AlmacenImagenNoticia, AlmacenMedioGaleria)
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        bcmath \
        intl \
        gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencias PHP de producción (sin dev, autoloader optimizado).
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

# Código de la aplicación.
COPY . .

# Assets compilados de la etapa 1.
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev

# Enlace de storage público (documentos, imágenes de noticias, galería).
RUN mkdir -p storage/app/public \
    && ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configuración del contenedor.
COPY deploy/php/php.ini                  /usr/local/etc/php/conf.d/99-produccion.ini
COPY deploy/nginx/default.conf           /etc/nginx/sites-available/default
COPY deploy/supervisor/supervisord.conf  /etc/supervisor/supervisord.conf

RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
