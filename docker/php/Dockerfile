# Set source
FROM php:7.4-fpm

# If we start multiple [php instances on single machine
#RUN sed -i 's/9000/3001/' /usr/local/etc/php-fpm.d/zz-docker.conf


# Set user/group
ENV USER=endemic
ENV GROUP=endemic

MAINTAINER Dima Denisov <mail@endemic.ru>

RUN groupadd -g 1000 ${USER}
RUN useradd -d /home/${USER} -s /bin/bash -u 1000 -g 1000 ${USER}

# User and group own home directory
RUN mkdir /home/${USER}
RUN chown -R ${USER}:${GROUP} /home/${USER}

# Install apcu
RUN pecl install apcu

# Install php
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        zlib1g-dev \
        g++ \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libpng-dev \
        libzip-dev \
        libmcrypt-dev \
        libicu-dev \
        libonig-dev \
        libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd exif \
    && docker-php-ext-install sockets \
    && docker-php-ext-configure intl \
    && docker-php-ext-install iconv mysqli bcmath pdo_mysql zip \
    && docker-php-ext-install intl

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add custom ini to php
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Set working dir
WORKDIR /var/www

# Set user
USER ${USER}

# start php-fpm
CMD ["php-fpm"]