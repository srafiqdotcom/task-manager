FROM php:8.2-fpm

# Install system dependencies including PostgreSQL client
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    unzip \
    nginx \
    supervisor \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql zip

# Configure nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Configure supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www/

# Set permissions
RUN mkdir -p /var/www/logs /var/www/var/cache/doctrine \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 777 /var/www/logs /var/www/var/cache

# Create the entry point script
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Install dependencies as root first
RUN composer install --no-interaction --optimize-autoloader

# Set proper permissions for vendor/bin executables
RUN chmod -R +x /var/www/vendor/bin

# Expose port 8080
EXPOSE 8080

# Start services
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
