FROM php:8.3-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite ET mod_headers (important!)
RUN a2enmod rewrite headers

# Augmenter la mémoire pour Composer
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copier Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Créer un .env minimal
RUN echo "APP_ENV=prod" > .env

# Copier composer.json et composer.lock
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --no-interaction \
        --prefer-dist

# Copier tout le code
COPY . .

# Générer l'autoloader
RUN composer dump-autoload \
        --optimize \
        --classmap-authoritative \
        --no-dev

# Config Apache AVEC headers CORS
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Header always set Access-Control-Allow-Origin "*"' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, PATCH, OPTIONS"' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Header always set Access-Control-Max-Age "3600"' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        FallbackResource /index.php' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Permissions
RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log && \
    chown -R www-data:www-data /var/www/html/var

# Créer le script d'entrypoint
RUN printf '#!/bin/bash\nset -e\necho "==> Waiting for database..."\nuntil php bin/console dbal:run-sql "SELECT 1" > /dev/null 2>&1; do\n  sleep 2\ndone\necho "==> Running migrations..."\nphp bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration\necho "==> Starting Apache..."\nexec apache2-foreground\n' > /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]