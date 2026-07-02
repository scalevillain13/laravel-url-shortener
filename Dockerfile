FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

COPY . .

RUN composer install --no-interaction --prefer-dist \
    && php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');" \
    && php artisan package:discover --ansi

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
