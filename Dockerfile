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

RUN echo "upload_max_filesize=2G" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size=2G" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "max_execution_time=1200" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "max_input_time=1200" >> /usr/local/etc/php/conf.d/uploads.ini
