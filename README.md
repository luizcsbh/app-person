
<p align="center">
  APP-PERSON
</p>
O projeto API REST desenvolvida em PHP com Laravel de acordo com o modelo de banco de dados abaixo:

![API-Crédito](https://github.com/luizcsbh/app-person/raw/main/assets/Modelo.jpg)

# Requesitos do Sistema
Linguagem de programação PHP.

Container com Sertvidor Min.io para armazenamento de objetos S3 https://min.io/.

Container com Servidor de banco de dados PostgreSQL.

# Requisitos Gerais
  A. Implementar mecanismo de autorização e autenticação, bem como não
permitir acesso ao endpoint a partir de domínios diversos do qual estará
hospedado o serviço;

  B. A solução de autenticação deverá expirar a cada 5 minutos e oferecer a
possibilidade de renovação do período;

  C. Implementar pelo menos os verbos post, put, get;

  D. Conter recursos de paginação em todas as consultas;

  E. Os dados produzidos deverão ser armazenados no servidor de banco
de dados previamente criado em container;

  F. Orquestrar a solução final utilizando Docker Compose de modo que inclua todos os contêineres utilizados.

# Requisitos Específicos:
Implementar uma API Rest para o diagrama de banco de dados acima
tomando por base as seguintes orientações:

Criar um CRUD para Servidor Efetivo, Servidor Temporário, Unidade e
Lotação. Deverá ser contemplado a inclusão e edição dos dados das
tabelas relacionadas;

Criar um endpoint que permita consultar os servidores efetivos lotados
em determinada unidade parametrizando a consulta pelo atributo unid_id;
Retornar os seguintes campos: Nome, idade, unidade de lotação e
fotografia;

Criar um endpoint que permita consultar o endereço funcional (da unidade
onde o servidor é lotado) a partir de uma parte do nome do servidor
efetivo.

Realizar o upload de uma ou mais fotografias enviando-as para o Min.IO;

A recuperação das imagens deverá ser através de links temporários
gerados pela biblioteca do Min.IO com tempo de expiração de 5 minutos.
Instruções:

A. O projeto deverá estar disponível no Github
- Crie um arquivo README.md contendo seus dados de inscrição
bem como orientações de como executar e testar a solução apresentada.

- Decorrido o prazo de entrega, nenhum outro commit deverá ser
enviado ao repositório do projeto.

- Adicione as dependências que considerar necessárias;

- Deverá estar disponível no repositório de versionamento todos
os arquivos e scripts utilizados para a solução.

# Como executar o Projeto

## Pré-requisitos
 - Docker e Docker Compose instalados;
 - Git para clonar o repositório

## 1. Clonar Repositório
Primeiro passo realizar o clone do repositório por ssh:

```ssh
git@github.com:luizcsbh/app-person.git
```
ou por git:

```git
git clone https://github.com/luizcsbh/app-person.git

cd app-person
````
## 2. Configurar variáveis de ambiente
Crie um arquivo .env baseado no .env.example e configure as variáveis

```env
cp .env.example .env
````
### 2.1. Variáveis de Usuário
O projeto utiliza as seguintes variáveis  para garantir as permissões corretas nos containers, no arquivo .env crie as linhas:

```ini
UID=1000  # User ID do usuário host
GID=1000  # Group ID do usuário host
```

### 2.2. Estrutura de Diretórios
Antes de iniciar os container, certifique que na raiz do projeto existe o diretório abaixo. Senão tiver crie a seguintes estruturas de diretórios:

```bash
mkdir -p docker/{postgres/conf,postgres/initdb,minio/config,volumes/postgres_data,volumes/minio_data}
```

### 2.3. Configuração de Permissões
Define as permissões para os volumes:

```bash
sudo chown -R $USER:$USER docker/volumes/
sudo chmod -R 775 docker/volumes/
```

## 3. Configuração do Docker
No arquivo .env do projeto configure conforme suas necessidades. As principais configurações incluem:

```ini
# Configurações do PostgreSQL
DB_HOST=db
DB_PORT=5432
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

#COnfiguração banco
POSTGRES_DB=seu_banco
POSTGRES_USER=seu_usuario
POSTGRES_PASSWORD=sua_senha

# Configurações do MinIO
FILESYSTEM_DISK=minio
MINIO_ENDPOINT=http://minio:9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET=seu_bucket
MINIO_URL=http://localhost:9000
```
certifique que variáveis estão defidamente configuradas e configure se for o caso no docker-compose 

### 3.1. Comandos para Execução 
#### 3.1.1 Construir e iniciar os containers:

```bash
docker=compose up -d --build
```
#### 3.1.2
Instalar dependências:

```bash
docker-compose exec app composer install
```
#### 3.1.3 Configurar aplicação:

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```
Observação: é necessário rodar a migration --seed para popular a tabela Cidades onde de 1 a 20 são cid_id cidades de São Paulo, 21 a 40 Cidades de Minas Gerais, 41 a 60 cidades do Rio de Janeiro, 61 a 80 cidades do Mato Grosso, 81 a 100 cidades do Distrito Federal e 101 a 120 cidades do Tocantis.


# 4. Acesando os Serviços

- Aplicação: http://localhost:8000
- MinIO Console: http://localhost:9001
- Swagger: http://localhost:8000/api/documentation
- Banco de Dados:
  - Host: db
  - Porta: 5432

# Notas Importantes
1. O diretório docker/volumes/ armazena dados persistentes,
2. Scripts SQL iniciais podem ser colocados em docker/postgres/initdb/
3. Configurações personalizadas do PostgreSQL podem ser adicionadas em docker/postgres/conf/

# 5. Parando os Containers
Para desligar o ambiente:

```bash
docker-compose down
```
Para remover complemente os volumes (cuidado - isso apagará todos os dados):

```bash
docker-compose down -v
```
# Criando um servidor efetivo
Para criar um servidor efetivo acesse a rota: http://localhost:8000/api/servidores-efetivos
 abaixo alguns modelos de arquivo json

```json
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
  }
  ```

  ```json
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
  }
  ```

  ```json
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
  }
  ```

  ```json
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
  }
  ```

  ```json
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
  }
  ```

  ```json
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
  }
  ```

  ```json
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
  ```
Para atualizar um servidor efetivo acesse a rota http://127.0.0.1:8000/api/servidores-efetivos/{id}
você pode atualizar todos os dados conforme o exemplo abaixo:

```json
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
  ```
ou dados especificos como exemplo:
```json
  {
    "pes_nome": "Gabriel Ferreira",
    "se_matricula": "2024-007",
    "pes_sexo": "Masculino",
    "end_tipo_logradouro": "Avenida",
    "end_logradouro": "das Saudades",
    "end_numero": 1345,
    "end_complemento": "Torre 1 ap.:601",
    "end_bairro": "Providência",
    "cid_id": 9
  }
  ```
Observação: essa regra se aplica para todas as rotas.

## Segurança Vulnerabilidades

Se você descobrir alguma vulnerabilidade de segurança por favor mande um e-mail para Luiz Santos via [luizcsdev@gmail.com](mailto:luizcsdev@gmail.com). 

## License

Esse projeto é open-source e é um software licenciado para [MIT license](https://opensource.org/licenses/MIT).
