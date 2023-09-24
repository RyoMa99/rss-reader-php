FROM --platform=linux/arm64 arm64v8/php:8.2-cli

RUN apt-get update &&\
  apt-get upgrade -y &&\
  apt-get install zip unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY src/ /usr/src/rss-reader/src
COPY composer.json /usr/src/rss-reader/


WORKDIR /usr/src/rss-reader/
RUN composer update &&\
  composer install

CMD [ "php", "src/main.php" ]
