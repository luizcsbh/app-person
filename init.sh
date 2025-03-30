#!/bin/bash

# Inicia os containers
docker-compose up -d

# Configura o MinIO (cria bucket)
docker exec minio_storage mc alias set local http://minio:9000 minioadmin minioadmin
docker exec minio_storage mc mb local/laravel
docker exec minio_storage mc policy set public local/laravel

# Executa migrações do Laravel
docker exec laravel_app php artisan migrate --seed --force

echo "Ambiente configurado com sucesso!"