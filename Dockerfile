# ================================
# 📦 BASE IMAGE
# ================================
FROM php:8.1-apache

# ================================
# 🧩 DEPENDÊNCIAS DO SISTEMA
# ================================
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev zip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd mbstring exif pcntl bcmath opcache

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
# (precisa vir antes do composer install, pois o helpers.php é carregado)
# ================================
COPY . .

# ================================
# ⚙️ INSTALA DEPENDÊNCIAS PHP
# ================================
RUN composer install --no-dev --no-interaction --optimize-autoloader

# ================================
# 🔑 ARQUIVO .env E CHAVE DO APP
# ================================
# Cria um .env se não existir (evita erro no build)
RUN if [ ! -f .env ]; then cp .env.example .env || touch .env; fi \
    && php artisan key:generate --force || true

# ================================
# 🔗 LINKS E PERMISSÕES
# ================================
RUN php artisan storage:link || true \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ================================
# 🧩 CONFIGURAÇÃO APACHE
# ================================
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# ================================
# 🔥 EXPOSE PORTA CORRETA
# ================================
EXPOSE 8080

# ================================
# 🚀 COMANDO DE EXECUÇÃO
# ================================
CMD ["apache2-foreground"]
