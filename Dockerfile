# --- Etapa base: PHP + Apache ---
FROM php:8.1-apache

# Instala dependências do Laravel
RUN apt-get update && apt-get install -y \
    zip unzip git libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilita o mod_rewrite (necessário para Laravel)
RUN a2enmod rewrite

# Copia o projeto para o container
WORKDIR /var/www/html
COPY . .

# Copia o .env do diretório de segredos do Render para o local padrão do Laravel
RUN if [ -f /etc/secrets/.env ]; then cp /etc/secrets/.env /var/www/html/.env; fi

# Define o DocumentRoot para a pasta "public"
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Instala o Composer e dependências
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Gera chave e cria storage link (se .env existir)
RUN if [ -f ".env" ]; then php artisan key:generate --force && php artisan storage:link; fi

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expõe a porta do Apache
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
