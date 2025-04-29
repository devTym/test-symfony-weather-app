FROM php:8.4-fpm

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www
COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install

EXPOSE 9000

CMD ["php-fpm"]