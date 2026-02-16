# ============================================
# Stage 1: Composer - Install PHP Dependencies
# ============================================
FROM php:8.2-cli-alpine AS composer

WORKDIR /app

# Install composer dependencies
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev

# Install PHP extensions required for composer install scripts
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip

# Get latest Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev

# ============================================
# Stage 2: Node - Build Frontend Assets
# ============================================
FROM node:20-alpine AS node

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY . .

RUN npm run build

# ============================================
# Stage 3: Production - PHP 8.2 FPM
# ============================================
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="BimaDevs"
LABEL description="School Library Management System"

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    shadow \
    bash

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        intl \
        bcmath \
        exif \
        pcntl \
        mbstring \
        xml \
        opcache

# Create system user
RUN addgroup -g 1000 -S www \
    && adduser -u 1000 -S www -G www

# Configure PHP-FPM to run as www
RUN sed -i 's/user = www-data/user = www/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = www/g' /usr/local/etc/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www/html

# Copy PHP config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy application code
COPY --chown=www:www . .

# Copy composer dependencies from stage 1
COPY --from=composer --chown=www:www /app/vendor ./vendor

# Copy built assets from stage 2
COPY --from=node --chown=www:www /app/public/build ./public/build

# Remove unnecessary files
RUN rm -rf node_modules \
    .git \
    .github \
    tests \
    docker \
    .env \
    .env.example \
    .editorconfig \
    .gitignore \
    .gitattributes \
    phpunit.xml \
    *.md \
    *.txt \
    *.sh \
    *.cjs

# Create required directories and set permissions
RUN mkdir -p \
        storage/app/public \
        storage/app/private \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www:www /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Use custom entrypoint
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
