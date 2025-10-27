FROM php:8.3-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite ET mod_headers
RUN a2enmod rewrite headers

ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN echo "APP_ENV=prod" > .env

COPY composer.json composer.lock symfony.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --no-interaction \
        --prefer-dist

COPY . .

RUN composer dump-autoload \
        --optimize \
        --classmap-authoritative \
        --no-dev

# Copier la config Apache
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log && \
    chown -R www-data:www-data /var/www/html/var

RUN printf '#!/bin/bash\nset -e\necho "==> Waiting for database..."\nuntil php bin/console dbal:run-sql "SELECT 1" > /dev/null 2>&1; do\n  sleep 2\ndone\necho "==> Running migrations..."\nphp bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration\necho "==> Starting Apache..."\nexec apache2-foreground\n' > /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]