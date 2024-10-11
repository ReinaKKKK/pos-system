FROM php:8.1-apache

RUN apt-get update && \
    apt-get install -y --no-install-recommends apt-utils vim zip unzip zlib1g git libpng-dev libjpeg-dev libzip-dev libreoffice-calc fonts-ipafont-gothic && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install -j$(nproc) mysqli pdo_mysql zip gd exif && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    a2enmod rewrite

RUN mkdir /var/www/libreoffice
RUN chown www-data:www-data /var/www/libreoffice

COPY --from=composer:2.4.0 /usr/bin/composer /usr/bin/composer

# run composer install
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/apache/httpd.conf /etc/apache2/sites-available/000-default.conf

RUN echo pwd: `pwd` && echo ls: `ls -l`

# environment
ARG BUILD_ENV
