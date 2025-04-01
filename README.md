
<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

[![lincença](https://img.shields.io/github/license/app-person)](https://github.com/luizcsbh/app-person/blob/main/LICENSE)


https://github.com/luizcsbh/app-person
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

  {
    "pes_nome": "Carlos Eduardo Silva",
    "se_matricula": "2024-001",
    "pes_data_nascimento": "2006-03-15",
    "pes_cpf": "145.876.234-90",
    "pes_sexo": "Masculino",
    "pes_mae": "Ana Paula Silva",
    "pes_pai": "José Carlos Silva",
    "end_tipo_logradouro": "Rua",
    "end_logradouro": "das Acácias",
    "end_numero": 125,
    "end_complemento": "Apto 302",
    "end_bairro": "Centro",
    "cid_id": 12
  },
  {
    "pes_nome": "Fernanda Lima Oliveira",
    "se_matricula": "2024-002",
    "pes_data_nascimento": "2005-11-28",
    "pes_cpf": "987.543.120-34",
    "pes_sexo": "Feminino",
    "pes_mae": "Beatriz Lima",
    "pes_pai": "Marcos Oliveira",
    "end_tipo_logradouro": "Avenida",
    "end_logradouro": "Brasil",
    "end_numero": 1500,
    "end_complemento": "Casa 2",
    "end_bairro": "Jardim América",
    "cid_id": 7
  },
  {
    "pes_nome": "Ricardo Alves Santos",
    "se_matricula": "2024-003",
    "pes_data_nascimento": "2007-01-10",
    "pes_cpf": "234.765.891-02",
    "pes_sexo": "Masculino",
    "pes_mae": "Patrícia Alves",
    "pes_pai": "Antônio Santos",
    "end_tipo_logradouro": "Travessa",
    "end_logradouro": "São Francisco",
    "end_numero": 45,
    "end_complemento": "",
    "end_bairro": "Vila Nova",
    "cid_id": 23
  },
  {
    "pes_nome": "Juliana Costa Pereira",
    "se_matricula": "2024-004",
    "pes_data_nascimento": "2006-07-22",
    "pes_cpf": "876.234.519-43",
    "pes_sexo": "Feminino",
    "pes_mae": "Márcia Costa",
    "pes_pai": "Roberto Pereira",
    "end_tipo_logradouro": "Rua",
    "end_logradouro": "XV de Novembro",
    "end_numero": 780,
    "end_complemento": "Fundos",
    "end_bairro": "Centro",
    "cid_id": 5
  },
  {
    "pes_nome": "Lucas Martins Rocha",
    "se_matricula": "2024-005",
    "pes_data_nascimento": "2005-09-05",
    "pes_cpf": "543.128.967-05",
    "pes_sexo": "Masculino",
    "pes_mae": "Tatiana Martins",
    "pes_pai": "Felipe Rocha",
    "end_tipo_logradouro": "Alameda",
    "end_logradouro": "dos Ipês",
    "end_numero": 230,
    "end_complemento": "Bloco B",
    "end_bairro": "Jardim das Flores",
    "cid_id": 18
  },
  {
    "pes_nome": "Amanda Souza Vieira",
    "se_matricula": "2024-006",
    "pes_data_nascimento": "2007-04-30",
    "pes_cpf": "321.654.098-76",
    "pes_sexo": "Feminino",
    "pes_mae": "Daniela Souza",
    "pes_pai": "Alexandre Vieira",
    "end_tipo_logradouro": "Avenida",
    "end_logradouro": "Paulista",
    "end_numero": 2200,
    "end_complemento": "Conjunto 45",
    "end_bairro": "Bela Vista",
    "cid_id": 31
  },
  {
    "pes_nome": "Gabriel Nunes Ferreira",
    "se_matricula": "2024-007",
    "pes_data_nascimento": "2006-12-12",
    "pes_cpf": "765.432.019-28",
    "pes_sexo": "Masculino",
    "pes_mae": "Simone Nunes",
    "pes_pai": "Paulo Ferreira",
    "end_tipo_logradouro": "Rua",
    "end_logradouro": "das Palmeiras",
    "end_numero": 89,
    "end_complemento": "",
    "end_bairro": "Vila Olímpia",
    "cid_id": 9
  }

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
