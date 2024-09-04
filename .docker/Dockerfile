FROM php:7.4-apache

RUN apt-get update \
    && apt-get install -y libxml2-dev git zip \
    && docker-php-ext-install soap

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www/

WORKDIR /var/www/
