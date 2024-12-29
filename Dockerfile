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

# Create necessary directories and set permissions
RUN mkdir -p /var/log/supervisor && \
    mkdir -p /tmp && \
    chmod 777 /tmp && \
    chown -R www-data:www-data /var/log/supervisor

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy and set permissions for entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Keep root as the user for supervisor
USER root

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]