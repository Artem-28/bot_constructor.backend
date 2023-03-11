# Arguments defined in docker-compose.yml
ARG user
ARG uid
ARG DOCKER_PHP_VERSION

FROM php:${DOCKER_PHP_VERSION}-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
# Clean
# RUN rm -rf /var/cache/apk/* && docker-php-source delete

# RUN curl -sS https://getcomposer.org/installer | php
# RUN mv composer.phar /usr/local/bin/composer

#COPY . /var/www/html
COPY . /var/www/html
COPY --chown=www:www . /var/www

# Set working directory
WORKDIR /var/www/html
USER www

EXPOSE 9000
CMD ["php-fpm"]
