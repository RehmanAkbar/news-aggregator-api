FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk update && apk add --no-cache \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    libxml2-dev \
    bash \
    shadow && \
    docker-php-ext-install pdo_mysql mbstring zip intl xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy PHP configuration
# COPY php.ini /usr/local/etc/php/conf.d/php.ini

# At this point, the www-data user already exists in the official PHP-FPM image
# No need to adduser/addgroup. Just ensure correct ownership:
RUN chown -R www-data:www-data /var/www/html

# Switch to non-root user
USER www-data

# COPY fpm-pool.conf /usr/local/etc/php-fpm.d/zzz-custom-pool.conf

CMD ["php-fpm"]
