FROM php:8.2-cli

# Install dependensi sistem & ekstensi PHP pdo_mysql
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- TAMBAHKAN BARIS INI BIAR COMPOSER NGGAK REWEL ---
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

# Jalankan server bawaan PHP dengan PORT dinamis bawaan Railway
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]