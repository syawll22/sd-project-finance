FROM php:8.2-fpm

# Install system dependencies & PHP extensions for MySQL
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx \
    && docker-php-ext-install pdo_mysql mysqli gd

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN sed -i 's/listen 80;/listen ${PORT};/g' /etc/nginx/sites-available/default

EXPOSE 8080

CMD php artisan config:clear && \
    php artisan storage:link --force && \
    nginx & php-fpm