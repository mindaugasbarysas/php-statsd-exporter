FROM nginx:latest
ADD conf.d/symfony.template /etc/nginx/conf.d/symfony.template
RUN mkdir -p /src/public && echo "" > /src/public/index.php
ENV PHP_HOST application-exporter
CMD ["/bin/bash" ,"-c", "cat /etc/nginx/conf.d/symfony.template | sed -e \"s/___PHP_HOST___/$PHP_HOST/g\" > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"]
