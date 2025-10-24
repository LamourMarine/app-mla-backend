FROM dunglas/frankenphp:latest

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed for Symfony
RUN install-php-extensions \
    pdo_pgsql \
    intl \
    zip \
    opcache \
    mbstring \
    iconv \
    curl \
    json \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy the whole project first
COPY . .

# Set permissions for Symfony
RUN chown -R www-data:www-data /app

# Install Symfony dependencies WITHOUT running post-install scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Clear and warmup cache in production
RUN php bin/console cache:clear --env=prod
RUN php bin/console cache:warmup --env=prod

# Expose ports
EXPOSE 80 443

# Start FrankenPHP
CMD ["frankenphp", "run"]