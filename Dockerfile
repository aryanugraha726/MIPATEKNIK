FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js artisan ./
COPY public ./public
RUN npm run build

FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress \
    && chown -R www-data:www-data storage bootstrap/cache \
    && a2enmod rewrite headers

EXPOSE 80