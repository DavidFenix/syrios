# ================================
# 📦 BASE IMAGE
# ================================
FROM php:8.1-apache

# ================================
# 🧩 DEPENDÊNCIAS DO SISTEMA
# ================================
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev zip curl libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mbstring exif pcntl bcmath opcache

# ================================
# 🧰 COMPOSER
# ================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ================================
# 📁 DIRETÓRIO DE TRABALHO
# ================================
WORKDIR /var/www/html

# ================================
# 📄 COPIA CÓDIGO DO PROJETO
# ================================
COPY . .

# ================================
# 🧱 CORRIGE DIRETÓRIOS E PERMISSÕES
# ================================
RUN mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} storage/logs \
    && touch storage/logs/laravel.log \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache storage

# ================================
# ⚙️ INSTALA DEPENDÊNCIAS PHP
# ================================
RUN composer install --no-dev --no-interaction --optimize-autoloader

# ================================
# 🔑 ARQUIVO .env E CHAVE DO APP
# ================================
RUN if [ ! -f .env ]; then cp .env.example .env || touch .env; fi \
    && php artisan key:generate --force || true

# ================================
# 🔗 LINKS E PERMISSÕES
# ================================
RUN php artisan storage:link || true \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache storage

# ================================
# ⚙️ CONFIGURAÇÃO DO APACHE
# ================================
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Habilita mod_rewrite e mod_headers (essenciais pro Laravel e cookies)
RUN a2enmod rewrite headers \
    && sed -i '/DocumentRoot \/var\/www\/html\/public/a<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>' /etc/apache2/sites-available/000-default.conf

# ⚠️ Importante:
# Não defina manualmente X-Forwarded-* — o Railway já faz isso.
# Apenas use TrustProxies no Laravel.

# ================================
# 🔥 LIMPEZA DE CACHE (importante para evitar conflitos)
# ================================
RUN php artisan config:clear || true && \
    php artisan cache:clear || true && \
    php artisan route:clear || true && \
    php artisan view:clear || true

# ================================
# 🔥 EXPOSE PORTA CORRETA
# ================================
EXPOSE 8080

# ================================
# 🚀 COMANDO FINAL (corrige permissões a cada start)
# ================================
CMD bash -c "\
    mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    touch storage/logs/laravel.log && \
    chown -R www-data:www-data storage bootstrap/cache storage && \
    chmod -R 775 storage bootstrap/cache storage && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    apache2-foreground"
