FROM php:7.3-fpm

RUN apt-get update

# Tools
RUN apt-get -y install mc nano

RUN apt-get update && apt-get install -y \
	libedit-dev libreadline-dev libxml2-dev libldap2-dev \
	libpng-dev libfreetype6-dev libjpeg62-turbo-dev libmpdec-dev \
	wget curl git libzip-dev

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) bcmath opcache mysqli pdo_mysql zip gd pcntl

RUN pecl install -o -f xdebug redis decimal \
  && docker-php-ext-enable xdebug redis decimal \
  && rm -rf /tmp/pear
