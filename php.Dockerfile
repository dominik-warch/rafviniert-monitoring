FROM php:8.2-fpm-alpine3.18

# Set working directory and copy files into container
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Install necessary alpine packages for PHP
RUN apk update && apk add --no-cache \
    zip \
    unzip \
    dos2unix \
    supervisor \
    libpng \
    libpng-dev \
    libzip-dev \
    freetype-dev \
    $PHPIZE_DEPS \
    libjpeg-turbo-dev \
    postgresql-libs \
    postgresql-dev \
    bzip2-dev \
    libsodium \
    libsodium-dev

# Configure PHP packages
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Compile native PHP packages
RUN docker-php-ext-install \
    gd \
    pcntl \
    bcmath \
    pgsql \
    pdo_pgsql \
    sodium \
    zip \
    bz2

# Install additional packages from PECL
RUN pecl install zip && docker-php-ext-enable zip \
    && pecl install msgpack && docker-php-ext-enable msgpack \
    && pecl install igbinary && docker-php-ext-enable igbinary \
    && yes | pecl install redis && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies via composer
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Install Node.js LTS
RUN apk add nodejs npm

# Install dependencies and build assets
RUN npm install \
    && npm run build

COPY ./php.ini /usr/local/etc/php/conf.d/99-monitoring.ini

USER www-data

EXPOSE 9000
CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
