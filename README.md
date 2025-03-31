<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Comandos para Construir e Executar
Crie a estrutura de diretórios:

bash
Copy
mkdir -p docker/{postgres/conf,postgres/initdb,minio/config,volumes/postgres_data,volumes/minio_data}
Defina as permissões:

bash
Copy
sudo chown -R $USER:$USER docker/volumes/
sudo chmod -R 775 docker/volumes/
Crie um arquivo .env na raiz do projeto:

ini
Copy
# Configurações do sistema
UID=1000
GID=1000

# Banco de dados
DB_PASSWORD=SuaSenhaForteAqui123!

# MinIO
MINIO_ACCESS_KEY=SeuAccessKeyMinIO
MINIO_SECRET_KEY=SuaSenhaForteMinIO123!
Construa e execute os containers:

bash
Copy
docker-compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) && docker-compose up -d
Execute os comandos de configuração do Laravel:

bash
Copy
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan migrate --seed
Configure o MinIO:

Acesse http://localhost:9001

Credenciais: minioadmin / minioadmin (ou as que você definiu no .env)

Crie um bucket chamado laravel-app

Verificação Final
Aplicação Laravel: http://localhost:8000

MinIO Console: http://localhost:9001

PostgreSQL: Verifique conexão na porta 5432

Comandos Adicionais Úteis
Reconstruir a aplicação:

bash
Copy
docker-compose build app && docker-compose up -d
Visualizar logs:

bash
Copy
docker-compose logs -f app
Acessar container:

bash
Copy
docker-compose exec app bash
```

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
