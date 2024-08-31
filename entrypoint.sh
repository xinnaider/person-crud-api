#!/bin/sh

# Copia o .env.example para .env se o arquivo .env não existir
if [ ! -f /var/www/.env ]; then
  echo "Arquivo .env não encontrado, copiando .env.example para .env"
  cp /var/www/.env.example /var/www/.env
fi

# Aguarda o banco de dados estar pronto
echo "Aguardando o banco de dados..."

while ! nc -z db 5432; do
  sleep 1
done

echo "Banco de dados PostgreSQL disponível!"

# Gera a chave da aplicação
php artisan key:generate

# Executa as migrações e popula o banco de dados
php artisan migrate --force
php artisan db:seed --force

# Gera a documentação Swagger
php artisan l5-swagger:generate

exec php artisan serve --host=0.0.0.0 --port=8000
