FROM php:8.3-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite
RUN a2enmod rewrite

# Augmenter la mémoire pour Composer
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copier Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Créer un .env minimal AVANT de copier le code
RUN echo "APP_ENV=prod" > .env

# Copier composer.json et composer.lock d'abord (pour le cache Docker)
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances en plusieurs étapes pour débugger
RUN composer validate --no-check-publish && \
    composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --no-interaction \
        --prefer-dist \
        --verbose

# Copier tout le reste du code
COPY . .

# Générer l'autoloader
RUN composer dump-autoload \
        --optimize \
        --classmap-authoritative \
        --no-dev

# Vérifier que autoload_runtime.php existe
RUN test -f /var/www/html/vendor/autoload_runtime.php || \
    (echo "ERROR: autoload_runtime.php not found!" && exit 1)

# Config Apache pour Symfony
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        FallbackResource /index.php\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Permissions
RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log && \
    chown -R www-data:www-data /var/www/html/var

EXPOSE 80

CMD ["apache2-foreground"]