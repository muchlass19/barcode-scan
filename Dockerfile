FROM php:8.3-apache

# Pindahkan direktori kerja ke standar Apache
WORKDIR /var/www/html

# Install library OS & ekstensi PHP (termasuk GD yang tadi)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip intl bcmath gd

# Aktifkan mod_rewrite Apache (wajib buat routing Laravel)
RUN a2enmod rewrite

# Arahkan DocumentRoot Apache langsung ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy semua file project
COPY . .

# Install dependencies Laravel
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Berikan akses write ke folder storage dan cache biar nggak error 500
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buka port 80 (standar web)
EXPOSE 80