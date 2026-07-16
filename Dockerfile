FROM dunglas/frankenphp:php8.4-bookworm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-interaction \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

EXPOSE 8080

CMD set -e && \
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && \
    php bin/console cache:clear --no-warmup --env=prod && \
    php bin/console cache:warmup --env=prod && \
    php bin/console assets:install public --env=prod && \
    php bin/console asset-map:compile --env=prod && \
    exec frankenphp run --config /app/Caddyfile