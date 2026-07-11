# ==========================
# Stage 1 - Build Frontend
# ==========================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

RUN npm run build


# ==========================
# Stage 2 - Build PHP
# ==========================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

COPY . .

RUN composer dump-autoload --optimize


# ==========================
# Stage 3 - Production Image
# ==========================
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    bash \
    sqlite \
    sqlite-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    linux-headers

# PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        intl \
        zip \
        exif \
        gd \
        bcmath \
        opcache

# Copy project
COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy nginx config
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]