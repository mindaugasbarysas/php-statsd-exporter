FROM redis
RUN echo "timeout 0\ntcp-keepalive 0" >> /etc/redis.conf
CMD [ "redis-server", "/etc/redis.conf" ]