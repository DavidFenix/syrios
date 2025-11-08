FROM php:8.1-apache

# Laravel precisa de mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Requisitos mínimos para Composer funcionar
RUN apt-get update && apt-get install -y \
    unzip zip git \
    && rm -rf /var/lib/apt/lists/*

# Extensões PHP mínimas para Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o projeto
COPY . .

# Instala dependências Laravel
RUN composer install --no-dev --no-interaction --optimize-autoloader

CMD ["apache2-foreground"]
