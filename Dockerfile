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
    && docker-php-ext-install pdo pdo_pgsql gd mbstring exif pcntl bcmath opcache

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
# ⚙️ INSTALA DEPENDÊNCIAS PHP
# ================================
RUN composer install --no-dev --no-interaction --optimize-autoloader

# ================================
# 🔑 ARQUIVO .env E CHAVE DO APP
# ================================
# Garante que exista um .env e gera APP_KEY
RUN if [ ! -f .env ]; then cp .env.example .env || touch .env; fi \
    && php artisan key:generate --force || true

# ================================
# 🔗 LINKS E PERMISSÕES
# ================================
RUN php artisan storage:link || true \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ================================
# ⚙️ CONFIGURAÇÃO DO APACHE
# ================================
# Define a pasta public como raiz
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Habilita mod_rewrite (necessário pro Laravel funcionar)
RUN a2enmod rewrite \
    && sed -i '/DocumentRoot \/var\/www\/html\/public/a<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>' /etc/apache2/sites-available/000-default.conf

# ================================
# 🔥 EXPOSE PORTA CORRETA
# ================================
EXPOSE 8080

# ================================
# 🚀 COMANDO DE EXECUÇÃO
# ================================
CMD ["apache2-foreground"]
