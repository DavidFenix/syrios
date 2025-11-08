FROM ghcr.io/railwayapp/php:8.1-apache

WORKDIR /app

COPY . .

RUN composer install --no-interaction --no-dev --optimize-autoloader

CMD ["apache2-foreground"]
