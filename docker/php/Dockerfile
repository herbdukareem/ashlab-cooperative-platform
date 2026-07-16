FROM php:8.3-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN apk add --no-cache \
        bash curl git icu-dev libzip-dev linux-headers mysql-client oniguruma-dev unzip zip \
    && docker-php-ext-install -j$(nproc) bcmath intl mbstring pcntl pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && addgroup -g ${GID} app \
    && adduser -D -u ${UID} -G app app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-platform.ini

WORKDIR /var/www/html
USER app

CMD ["php-fpm"]

