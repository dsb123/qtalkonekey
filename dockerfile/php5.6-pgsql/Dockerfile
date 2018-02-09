# sudo docker build -t php:5.6-apache-pgsql-redis .
FROM php:5.6-apache

RUN apt-get update

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql

ENV PHPREDIS_VERSION 3.1.3
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-$PHPREDIS_VERSION /usr/src/php/ext/redis \
    && docker-php-ext-install redis \
    && rm -rf /usr/src/php #如果这段不加构建的镜像将大100M 