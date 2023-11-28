FROM php:8-fpm-alpine

ENV PHPGROUP=laravel
ENV PHPUSER=laravel

RUN adduser -g ${PHPGROUP} -s /bin/sh -D ${PHPUSER}

RUN sed -i "s/user = www-data/user = ${PHPUSER}/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = ${PHPGROUP}/g" /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /var/www/html/public

RUN set -ex \
	&& apk --no-cache add postgresql-libs postgresql-dev bzip2-dev libzip-dev zip libpng libpng-dev libsodium-dev libsodium \
	&& docker-php-ext-install bz2 gd pgsql pdo_pgsql sodium zip

RUN pear update-channels
RUN pecl update-channels

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev ${PHPIZE_DEPS}

COPY php/php.ini /usr/local/etc/php/conf.d/99-monitoring.ini

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
