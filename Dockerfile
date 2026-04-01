FROM dunglas/frankenphp:latest-php8.2

RUN docker-php-ext-install mysqli pdo_mysql

WORKDIR /app/public

COPY . .

ENV SERVER_NAME=:8080

EXPOSE 8080
