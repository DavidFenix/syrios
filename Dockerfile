FROM php:8.1-apache

# Habilita módulos necessários
RUN a2enmod rewrite

# Instala dependências essenciais
RUN apt-get update && apt-get install -y \
    libjpeg-dev libfreetype6-dev libzip-dev unzip git \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_mysql gd zip

# Copia o projeto para o diretório padrão do Apache
WORKDIR /var/www/html
COPY . /var/www/html

# Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Ativa .htaccess do Laravel
RUN sed -i "s/AllowOverride None/AllowOverride All/" /etc/apache2/apache2.conf

# Ajusta porta para a do Railway
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!Listen 80!Listen 8080!g' /etc/apache2/ports.conf

EXPOSE 8080
CMD ["apache2-foreground"]
