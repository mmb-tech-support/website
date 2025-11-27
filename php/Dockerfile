FROM php:8.2-fpm-alpine

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        zlib-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd mysqli zip \
    && apk add --no-cache \
        libpng \
        libjpeg-turbo \
        freetype \
        libzip \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

COPY ./conf.d/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./src/ /var/www/html/

RUN addgroup -g 1000 php \
    && adduser -D -u 1000 -G php php \
    && chown -R php:php /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && mkdir -p /var/log/php \
    && chown -R php:php /var/log/php \
    && chmod -R 750 /var/log/php

USER php

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]