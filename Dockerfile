FROM php:8.2-apache
WORKDIR /var/www/html
COPY . .
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y unzip
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN composer install
EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
