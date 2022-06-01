FROM php:7.3.33-apache

RUN apt-get update \
 && apt-get install -y git zlib1g-dev libicu-dev \
 && docker-php-ext-install pdo pdo_mysql zip intl \
 && a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
