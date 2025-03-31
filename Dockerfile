FROM php:8.2-fpm

# Define variáveis de ambiente para UID/GID
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    acl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Instala extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Configura usuário e grupo para sincronizar com o host
RUN groupmod -g ${GROUP_ID} www-data && \
    usermod -u ${USER_ID} -g ${GROUP_ID} www-data

# Configura o Git para o diretório da aplicação
RUN git config --global --add safe.directory /var/www/html

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos da aplicação (ignorando o .dockerignore)
COPY . .

# Ajusta permissões antes da instalação do Composer
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    setfacl -Rdm u:www-data:rwx /var/www/html/storage /var/www/html/bootstrap/cache && \
    setfacl -Rm u:www-data:rwx /var/www/html/storage /var/www/html/bootstrap/cache

# Instala dependências do Laravel como www-data
USER www-data
RUN composer install --optimize-autoloader --no-dev --prefer-dist

# Volta para root para ajustes finais
USER root

# Expõe a porta e inicia o servidor
EXPOSE 8000
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=8000"]