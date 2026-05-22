FROM php:8.2-apache

ARG APP_ENV=production
ARG APP_DEBUG=false

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libavif-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-avif \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY . /var/www/html/

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN set -ex \
    && mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN sed -i 's|upload_max_filesize = 2M|upload_max_filesize = 100M|' "$PHP_INI_DIR/php.ini" \
    && sed -i 's|post_max_size = 8M|post_max_size = 108M|' "$PHP_INI_DIR/php.ini" \
    && sed -i 's|max_execution_time = 30|max_execution_time = 300|' "$PHP_INI_DIR/php.ini"

ENV APP_ENV=${APP_ENV} \
    APP_DEBUG=${APP_DEBUG}

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
