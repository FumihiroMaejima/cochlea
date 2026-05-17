# FROM redis:7.2-alpine
FROM valkey/valkey:8.1-alpine

COPY ./redis.conf /usr/local/etc/redis/redis.conf
# /usr/local/etc/redis内のファイル参照の為権限設定
RUN chown -R redis.redis /usr/local/etc/redis

# CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]
CMD ["valkey-server", "/usr/local/etc/redis/redis.conf"]
