FROM php:8.3-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev openssl \
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

# Installer les dépendances PHP SANS scripts
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
        --verbose

# Copier tout le reste du code
COPY . .

# Regénérer l'autoloader sans exécuter les scripts Symfony
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Créer manuellement les répertoires de cache (au lieu de cache:clear)
RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data var
    
# Vérifier que autoload_runtime.php existe
RUN test -f /var/www/html/vendor/autoload_runtime.php || \
    (echo "ERROR: autoload_runtime.php not found!" && exit 1)

# -------------------------
# AJOUT : Génération automatique des clés JWT
# -------------------------
ARG JWT_PASSPHRASE
ENV JWT_PASSPHRASE=$JWT_PASSPHRASE

RUN mkdir -p config/jwt && \
    openssl genrsa -aes256 -passout pass:$JWT_PASSPHRASE -out config/jwt/private.pem 4096 && \
    openssl rsa -pubout -passin pass:$JWT_PASSPHRASE -in config/jwt/private.pem -out config/jwt/public.pem && \
    chmod 600 config/jwt/private.pem config/jwt/public.pem && \
    echo "JWT keys generated successfully."

# Copier les clés dans le conteneur
COPY config/jwt /var/www/html/config/jwt
RUN chown -R www-data:www-data /var/www/html/config/jwt && \
    chmod 600 /var/www/html/config/jwt/private.pem && \
    chmod 644 /var/www/html/config/jwt/public.pem

# -------------------------

# Config Apache pour Symfony
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Permissions
RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log && \
    chown -R www-data:www-data /var/www/html/var

EXPOSE 80

CMD ["apache2-foreground"]
