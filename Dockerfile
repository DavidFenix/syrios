# --- Etapa base: PHP + extensões necessárias ---
FROM php:8.1-apache

# Instala dependências do Laravel
RUN apt-get update && apt-get install -y \
    zip unzip git libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilita o mod_rewrite (necessário para Laravel)
RUN a2enmod rewrite

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos do projeto
COPY . .

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Cria storage link e gera chave da aplicação
RUN php artisan key:generate --force && php artisan storage:link || true

# Define permissões corretas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expõe a porta padrão do Apache
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
