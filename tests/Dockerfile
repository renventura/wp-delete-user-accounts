# Test runner image. PHP_VERSION lets the same image cover the supported range.
ARG PHP_VERSION=8.6
FROM php:${PHP_VERSION}-cli
RUN apt-get update && apt-get install -y --no-install-recommends git unzip default-mysql-client libonig-dev \
	&& (php -m | grep -qi mbstring || docker-php-ext-install mbstring) && docker-php-ext-install mysqli \
	&& rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
