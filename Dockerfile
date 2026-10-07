FROM php:8.2-fpm

# Install system dependencies & extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install dependencies by bypassing security blocking globally
RUN composer config --global process-timeout 2000 && \
    composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Setup Nginx configuration for Railway port
RUN sed -i 's/listen 80;/listen ${PORT};/g' /etc/nginx/sites-available/default

EXPOSE 8080

CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan storage:link --force && \
    nginx & php-fpm     