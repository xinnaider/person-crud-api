# Dockerfile

FROM php:8.2

# Instalações necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    zip \
    netcat-openbsd \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos da aplicação
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Script de entrada
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["./entrypoint.sh"]

EXPOSE 8000
