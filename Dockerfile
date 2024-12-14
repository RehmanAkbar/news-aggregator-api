# Dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions for Laravel
RUN chown -R www:www /var/www && \
    chmod -R 755 /var/www && \
    find /var/www/storage -type f -exec chmod 664 {} \; && \
    find /var/www/storage -type d -exec chmod 775 {} \; && \
    find /var/www/bootstrap/cache -type d -exec chmod 775 {} \; && \
    find /var/www/bootstrap/cache -type f -exec chmod 664 {} \;

# Expose port 9000
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
