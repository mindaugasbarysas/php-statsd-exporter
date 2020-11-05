FROM php:7.4-fpm
RUN docker-php-ext-install sockets
WORKDIR /src
RUN apt-get update && apt-get -qy install bash wget zip git
ADD . /src
RUN bash /src/infrastructure/prod/composer-install.sh
RUN php composer update && php composer install