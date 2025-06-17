# Multi-stage build for Laravel application
FROM node:22-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install npm dependencies
RUN npm ci --only=production

# Copy source code and build assets
COPY . .
RUN npm run build

# PHP Application Stage
FROM php:8.2-fpm-alpine AS php-base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    sqlite3 \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create application directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application code
COPY . .

# Copy built assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create necessary directories
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

USER www-data

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
