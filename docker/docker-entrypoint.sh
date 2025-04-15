#!/bin/bash
set -e

# Create logs directory if it doesn't exist
mkdir -p /var/www/logs
chown -R www-data:www-data /var/www/logs
chmod -R 777 /var/www/logs

# Create var/cache directory for Doctrine if it doesn't exist
mkdir -p /var/www/var/cache/doctrine
chown -R www-data:www-data /var/www/var/cache
chmod -R 777 /var/www/var/cache

# Fix permissions for the entire project
find /var/www -type d -exec chmod 755 {} \;
find /var/www -type f -exec chmod 644 {} \;
find /var/www/vendor/bin -type f -exec chmod +x {} \;
chown -R www-data:www-data /var/www

# Wait for the database to be ready
echo "Waiting for PostgreSQL to be ready..."
export PGPASSWORD=$DB_PASSWORD
until pg_isready -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE; do
  echo "PostgreSQL is unavailable - sleeping"
  sleep 1
done
echo "PostgreSQL is up - executing migrations"

# Run migrations and seed the database directly as root
echo "Running database migrations..."
cd /var/www && php vendor/bin/phinx migrate -e development

echo "Seeding the database..."
cd /var/www && php vendor/bin/phinx seed:run

# Execute the CMD
exec "$@"
