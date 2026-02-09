FROM php:8.3-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip unzip git curl nginx gettext-base


# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd zip

# Copy app
WORKDIR /var/www/html
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Nginx config
RUN rm /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/templates/default.conf.template


# Permission
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80

CMD envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf \
    && php-fpm -F & nginx -g 'daemon off;'

