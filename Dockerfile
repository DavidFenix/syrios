# 🧩 Etapa 1: Build Composer
FROM composer:2.6 AS build

WORKDIR /app

# Copia arquivos e instala dependências
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Copia todo o código
COPY . .

RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# 🧩 Etapa 2: Servidor PHP 8.1 + Apache
FROM php:8.1-apache

# Instala extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite (necessário para Laravel)
RUN a2enmod rewrite

# Copia app da etapa anterior
COPY --from=build /app /var/www/html

WORKDIR /var/www/html

# ✅ Corrige permissões
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ✅ Cria .env vazio se não existir e gera APP_KEY
RUN touch /var/www/html/.env \
    && php artisan key:generate --force || true \
    && php artisan storage:link || true

# ✅ Expõe porta 8080 e inicia o servidor Laravel
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
