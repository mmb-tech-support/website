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
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

COPY ./src/ /var/www/html/

RUN addgroup -g 1000 website \
    && adduser -D -u 1000 -G website website \
    && chown -R root:website /var/www/html \
    && chmod -R 444 /var/www/html

USER website

WORKDIR /var/www/html

EXPOSE 9000