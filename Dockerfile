# --- Base PHP + Apache
FROM php:8.1-apache

# Deixe o Apache ouvir na porta que o Railway injeta
ENV APACHE_DOCUMENT_ROOT=/app/public
ENV PORT=8080

# Dependências mínimas + GD com JPEG
RUN apt-get update && apt-get install -y \
    libjpeg-dev libfreetype6-dev libzip-dev unzip git \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql gd zip \
 && rm -rf /var/lib/apt/lists/*

# Composer (oficial) para instalar dependências
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Código
WORKDIR /app
COPY . .

# Ajusta DocumentRoot do Apache para /app/public e porta
RUN sed -ri -e 's!/var/www/html!/app/public!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!Listen 80!Listen 8080!g' /etc/apache2/ports.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
 && a2enmod rewrite

# Permissões básicas para cache e sessão
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Instala vendor em modo produção
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Opcional: gerar chave se não houver
# RUN php artisan key:generate --force

# Dica: não rode comandos artisan que dependam de DB no build
# (migrations/seed) — rode em release/console depois se precisar

EXPOSE 8080
CMD ["apache2-foreground"]
