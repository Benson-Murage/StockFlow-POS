# Production Dockerfile for StockFlowPOS Laravel + React application
# Optimized for Render deployment

# ==============================================
# Stage 1: Build dependencies
# ==============================================
FROM node:20-alpine AS node-builder

WORKDIR /var/www/html

# Copy package files
COPY package*.json ./

# Install Node.js dependencies
RUN npm ci --only=production && npm cache clean --force

# Copy source code and build assets
COPY . .

# Build frontend assets for production
RUN npm run build

# ==============================================
# Stage 2: PHP dependencies and application
# ==============================================
FROM php:8.3-fpm-alpine AS php-builder

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    gcc \
    git \
    g++ \
    icu-dev \
    icu-libs \
    icu \
    icu-dev \
    imagemagick-dev \
    libc-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    make \
    mysql-client \
    mysql-dev \
    nginx \
    openssh-client \
    rsync \
    zlib-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        mysqli \
        pdo \
        pdo_mysql \
        zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ==============================================
# Stage 3: Production image
# ==============================================
FROM php:8.3-fpm-alpine AS production

# Install runtime dependencies
RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    gcc \
    g++ \
    icu-dev \
    icu-libs \
    icu \
    icu-dev \
    imagemagick-dev \
    libc-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    make \
    mysql-client \
    mysql-dev \
    nginx \
    openssh-client \
    rsync \
    supervisor \
    zlib-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        mysqli \
        pdo \
        pdo_mysql \
        zip

# Copy built PHP dependencies
COPY --from=php-builder /var/www/html/vendor /var/www/html/vendor

# Copy built frontend assets
COPY --from=node-builder /var/www/html/public/build /var/www/html/public/build

# Set working directory
WORKDIR /var/www/html

# Copy application code (excluding sensitive files)
COPY --chown=www-data:www-data . .

# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy Nginx configuration
COPY deployment/nginx.conf /etc/nginx/nginx.conf

# Copy PHP configuration
COPY deployment/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy supervisord configuration
COPY deployment/supervisord.conf /etc/supervisord.conf

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose ports
EXPOSE 8080

# Switch to non-root user
USER www-data

# Set entrypoint
ENTRYPOINT ["/bin/sh", "-c"]

# Start services
CMD ["supervisord -c /etc/supervisord.conf"]