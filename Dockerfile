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
    shadow \
    supervisor \
    mysql-client && \
    docker-php-ext-install pdo_mysql mbstring zip intl xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Create supervisor log directory
RUN mkdir -p /var/log/supervisor

# Set up users and permissions
RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data && \
    chown -R www-data:www-data /var/www/html && \
    chown -R www-data:www-data /var/log/supervisor

# Give necessary permissions to supervisor directory
RUN mkdir -p /var/run && chown -R www-data:www-data /var/run

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Switch to www-data user
USER www-data

# Keep the container running
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]