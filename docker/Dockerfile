FROM php:8.2-cli

# System deps
RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        unzip \
        libpq-dev \
        libmysqlclient-dev \
        libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ── Install PHP deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize \
    && php artisan config:clear \
    && php artisan route:clear

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
