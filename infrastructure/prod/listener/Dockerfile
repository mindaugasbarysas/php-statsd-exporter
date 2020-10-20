FROM php:7.4-cli
RUN docker-php-ext-install sockets
WORKDIR /src
RUN apt-get update && apt-get -qy install bash wget
ENV HOST=0.0.0.0 PORT=9125
ADD . /src
RUN bash /src/infrastructure/prod/composer-install.sh
RUN php composer install
CMD ["/bin/bash", "-c", "php bin/console statsd:listen-udp --host $HOST --port $PORT"]