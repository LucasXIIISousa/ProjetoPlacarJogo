FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    curl \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install zip pdo pdo_sqlite

# Copiar o código do projeto para dentro do container
WORKDIR /var/www/html
COPY . .

# Criar as pastas 'storage' e 'bootstrap/cache' se não existirem
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Definir permissões corretas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
