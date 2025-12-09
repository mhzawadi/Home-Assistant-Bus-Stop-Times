FROM alpine:3.23
LABEL org.opencontainers.image.authors="matt@horwood.biz"

# Install required deb packages
RUN apk update && apk upgrade && \
    apk add gnupg unit-php84 php84-simplexml php84-xmlwriter php84-tokenizer \
    php84-curl php84 php84-phar curl git \
    && mkdir -p /var/www/html/ \
    && mkdir -p /run/nginx \
    && rm -f /var/cache/apk/*; \
    [ -f /usr/bin/php ] && rm -f /usr/bin/php; \
    ln -s /usr/bin/php84 /usr/bin/php;

COPY docker/config /config

WORKDIR /var/www/html
COPY src/* .
RUN cp /config/php.ini /etc/php84/php.ini \
  && chown nobody:nobody * -R

EXPOSE 8080
ENTRYPOINT ["/config/start.sh"]
CMD ["unitd", "--no-daemon", "--control", "unix:/var/run/control.unit.sock"]

## Health Check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s \
  CMD curl -f http://127.0.0.1:8080 || exit 1
