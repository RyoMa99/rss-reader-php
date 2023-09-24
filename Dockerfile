FROM php:8.2-cli

RUN apt update &&\
  apt upgrade -y &&\
  apt install zip unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY src/ /usr/src/rss-reader/src
COPY composer.* /usr/src/rss-reader/


WORKDIR /usr/src/rss-reader/
RUN composer install

CMD [ "php", "src/main.php" ]
