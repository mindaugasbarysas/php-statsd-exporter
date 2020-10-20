FROM keinos/php8-jit:latest
USER root
RUN docker-php-ext-install sockets
WORKDIR /src
RUN apk add bash