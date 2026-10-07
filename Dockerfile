FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx \
    && docker-php-ext-install pdo_mysql mysqli gd

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ubah document root Nginx ke folder public Laravel
RUN sed -i 's|root /var/www/html;|root /var/www/html/public;|g' /etc/nginx/sites-available/default
RUN sed -i 's/listen 80;/listen ${PORT};/g' /etc/nginx/sites-available/default

EXPOSE 8080

CMD php artisan config:clear && \
    php artisan storage:link --force && \
    nginx & php-fpm