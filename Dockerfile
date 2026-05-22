FROM php:8.2-apache-bookworm

ARG APP_ENV=production
ARG APP_DEBUG=false

# ── Dependencias del sistema ──
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libavif-dev \
        libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# ── Extensión GD ──
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-avif \
    && docker-php-ext-install -j4 pdo_mysql pdo_pgsql gd

# ── Apache ──
RUN a2enmod rewrite

# ── Aplicación ──
COPY . /var/www/html/
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# ── PHP ini ──
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's|upload_max_filesize = 2M|upload_max_filesize = 100M|' "$PHP_INI_DIR/php.ini" \
    && sed -i 's|post_max_size = 8M|post_max_size = 108M|' "$PHP_INI_DIR/php.ini" \
    && sed -i 's|max_execution_time = 30|max_execution_time = 300|' "$PHP_INI_DIR/php.ini"

ENV APP_ENV=${APP_ENV} \
    APP_DEBUG=${APP_DEBUG}

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
