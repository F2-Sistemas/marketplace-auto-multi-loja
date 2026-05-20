FROM php:8.2-cli-alpine

# Install system dependencies and build libraries
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Expose Laravel dev server port
EXPOSE 7031

# Default command to serve the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=7031"]
