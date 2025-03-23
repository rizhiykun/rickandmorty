FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libicu-dev libonig-dev \
    postgresql-client \  
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install

CMD ["php-fpm"]
