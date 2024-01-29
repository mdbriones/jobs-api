# Stage 1: Build PHP image with Composer
FROM composer:2 as composer

WORKDIR /var/www/html

# Copy only the composer files needed for installation
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --ignore-platform-reqs --no-scripts --no-autoloader --prefer-dist

# Stage 2: Build PHP image without Composer
FROM php:8.1-fpm

WORKDIR /var/www/html

# Copy only the necessary files from the Composer image
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /var/www/html/vendor/ /var/www/html/vendor/

# Copy the rest of the application files
COPY . .

# Set up Laravel and permissions
RUN composer dump-autoload --optimize && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
