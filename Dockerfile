FROM php:8.1-apache

# Extensões PHP mínimas necessárias para Laravel
RUN apt-get update && apt-get install -y \
    git unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mbstring tokenizer xml

# Instala Composer (mínimo necessário)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia aplicação
COPY . .

# Permissões mínimas
RUN mkdir -p storage framework cache bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Instala dependências (mínimo)
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Apache aponta para /public
RUN sed -i 's#/var/www/html#/var/www/html/public#g' \
    /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# Porta padrão Railway
EXPOSE 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
