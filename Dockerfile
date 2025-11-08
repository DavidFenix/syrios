FROM php:8.1-apache

# Habilita mod_rewrite (Laravel precisa)
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copia o projeto
COPY . .

# Instala extensões mínimas necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instala dependências Laravel
RUN composer install --no-dev --no-interaction --optimize-autoloader

CMD ["apache2-foreground"]
