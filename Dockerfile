# Use official PHP + Apache image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Fix Laravel storage/cache permissions
RUN chmod -R 775 storage bootstrap/cache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Install unzip for Composer
RUN apt-get update && apt-get install -y unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Run migrations and clear caches
RUN php artisan migrate --force || true
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Expose port 80
EXPOSE 80

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
