FROM php:7.2
COPY . /var/www
WORKDIR /var/www
RUN docker-php-ext-install mysqli pdo_mysql
CMD [ "php", "-S", "0.0.0.0:8080", "-t", "."]