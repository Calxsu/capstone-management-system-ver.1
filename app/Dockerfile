FROM php:8.2-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libonig-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring zip bcmath intl gd opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-custom.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
