FROM php:8.2-fpm-alpine

RUN docker-php-ext-install pcntl

RUN apk --update add --virtual build-dependencies build-base openssl-dev autoconf \
  && pecl install mongodb\
  && docker-php-ext-enable mongodb \
  && apk del build-dependencies build-base openssl-dev autoconf \
  && rm -rf /var/cache/apk/*
