# --- Etapa base: PHP + Apache ---
FROM php:8.1-apache

# Instala dependências do Laravel
RUN apt-get update && apt-get install -y \
    zip unzip git libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilita o mod_rewrite (necessário para Laravel)
RUN a2enmod rewrite

# Copia o projeto Laravel
WORKDIR /var/www/html
COPY . .

# 🔧 Se existir o .env do Render, copia para o Laravel antes de instalar dependências
RUN if [ -f /etc/secrets/.env ]; then \
      echo "✔ Copiando .env de /etc/secrets para /var/www/html"; \
      cp /etc/secrets/.env /var/www/html/.env; \
    else \
      echo "⚠️ Nenhum arquivo /etc/secrets/.env encontrado"; \
    fi

# Define o DocumentRoot para a pasta "public"
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Copia o Composer do container oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 🔧 Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader || true

# Gera chave e cria storage link (sem erro se .env não existir)
RUN php artisan key:generate --force || true && php artisan storage:link || true

# Ajusta permissões de cache e storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponha a porta HTTP
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
