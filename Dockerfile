FROM dunglas/frankenphp

RUN install-php-extensions mysqli pdo_mysql

COPY . /app/public

EXPOSE 8080
