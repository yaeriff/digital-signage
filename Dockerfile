FROM php:8.3-cli

# Install system deps
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip unzip git curl

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd zip

# Set workdir
WORKDIR /app
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader
RUN php artisan storage:link || true


RUN php artisan config:clear \
 && php artisan cache:clear \
 && php artisan view:clear
RUN chmod -R 775 storage bootstrap/cache


# Railway provides PORT
CMD php -S 0.0.0.0:${PORT} -t public

RUN php artisan migrate --force || true
