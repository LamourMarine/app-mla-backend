#!/bin/bash
set -e

echo "Waiting for database..."
until php bin/console dbal:run-sql "SELECT 1" > /dev/null 2>&1; do
  echo "Database not ready, waiting..."
  sleep 2
done

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "Starting Apache..."
exec apache2-foreground