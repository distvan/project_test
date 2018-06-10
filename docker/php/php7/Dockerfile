FROM php:7.1-fpm
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data