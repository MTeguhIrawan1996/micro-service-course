# Use an official PHP image as the base image
FROM php:7.4-apache

# Copy composer.json and composer.lock to the container
COPY composer.json composer.lock ./

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && a2enmod rewrite

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run composer install to install Laravel and its dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code to the container
COPY . .

# Generate the autoload file
RUN composer dump-autoload

# Expose port 80
EXPOSE 8000

# Set the entrypoint to the apache2-foreground command
ENTRYPOINT ["apache2-foreground", "-D", "FOREGROUND"]
