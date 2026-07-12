FROM ghcr.io/serversideup/php:8.4-fpm-nginx-alpine

ARG PHP_POST_MAX_SIZE="100M"
ARG PHP_UPLOAD_MAX_FILE_SIZE="100M"
ARG PHP_MEMORY_LIMIT="1024M"
ARG AUTORUN_ENABLED="true"
ARG LOG_OUTPUT_LEVEL="debug"
ARG PHP_DATE_TIMEZONE="Asia/Riyadh"

USER root

RUN apk --no-cache add \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    bash \
    fcgi \
    libmcrypt-dev \
    oniguruma-dev \
    nodejs \
    npm

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN install-php-extensions exif gd intl pdo pdo_mysql mbstring zip exif pcntl bcmath opcache

WORKDIR /var/www/html

# Copy app source and install dependencies
COPY --chown=www-data:www-data ./src/ ./
RUN composer install

# Build frontend assets (after composer so vendor exists for Filament theme)
RUN npm install && npm run build

# Laravel setup
RUN php artisan cache:clear
RUN php artisan view:clear
RUN php artisan clear-compiled
RUN php artisan route:clear
RUN php artisan storage:link
RUN php artisan icons:cache

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

USER www-data
