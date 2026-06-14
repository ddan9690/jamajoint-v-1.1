FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libjpeg-dev libfreetype6-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/jamajoint

# Copy application code
COPY . .

# Set permissions for Laravel to function properly
RUN chown -R www-data:www-data /var/www/jamajoint/storage /var/www/jamajoint/bootstrap/cache

# Expose port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]