FROM php:8.2-cli

WORKDIR /app
COPY . .

RUN apt-get update && apt-get install -y unzip git libzip-dev && \
    docker-php-ext-install pdo_mysql zip

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
