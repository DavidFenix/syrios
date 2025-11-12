# 1️⃣ Escolhe a imagem base
FROM php:8.1-apache

# 2️⃣ Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev

# 3️⃣ Configura extensões do PHP
RUN docker-php-ext-install pdo pdo_mysql zip gd

# 4️⃣ Define o diretório de trabalho
WORKDIR /var/www/html

# 5️⃣ Copia os arquivos do projeto para dentro da imagem
COPY . .

# 6️⃣ Expõe uma porta
EXPOSE 80

# 7️⃣ Comando padrão ao iniciar o container
CMD ["apache2-foreground"]
