# Usa imagem leve e moderna do PHP
FROM php:8.2-cli

# Define o diretório de trabalho
WORKDIR /app

# Copia todos os arquivos do projeto Laravel para dentro da imagem
COPY . .

# Instala dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y unzip git libzip-dev && \
    docker-php-ext-install pdo_mysql zip

# 🔹 Instala o Composer (copiando da imagem oficial do Composer)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 🔹 Instala as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Expõe a porta padrão usada pelo Railway
EXPOSE 8080

# Comando de inicialização do Laravel (servidor embutido)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
