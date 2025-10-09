# Use uma imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar arquivos da aplicação
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor porta 80
EXPOSE 80
