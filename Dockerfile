# --- Etapa base: PHP + Apache ---
FROM php:8.1-apache

# Instala dependências do Laravel
RUN apt-get update && apt-get install -y \
    zip unzip git libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilita o mod_rewrite (necessário para Laravel)
RUN a2enmod rewrite

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia o projeto para dentro do container
COPY . .

# Copia .env se existir em /etc/secrets (Render)
RUN if [ -f /etc/secrets/.env ]; then cp /etc/secrets/.env /var/www/html/.env; fi

# ⚙️ Configura o Apache para apontar para /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    echo "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# Instala o Composer e dependências do Laravel
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Gera chave e cria storage link
RUN php artisan key:generate --force || true && php artisan storage:link || true

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expõe a porta 80
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
