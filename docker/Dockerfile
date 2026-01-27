FROM alpine:3.23
LABEL org.opencontainers.image.authors="matt@horwood.biz"

# Install required deb packages
RUN apk update && apk upgrade && \
    apk add gnupg nginx php84-fpm php84-simplexml php84-xmlwriter php84-tokenizer \
    php84-curl php84 php84-phar curl git \
    && rm -f /var/cache/apk/* \
    && mkdir -p /var/www/html/ \
    && mkdir -p /run/nginx; \
    [ -f /usr/bin/php ] && rm -f /usr/bin/php; \
    ln -s /usr/bin/php83 /usr/bin/php;

COPY docker/config /config

WORKDIR /var/www/html
COPY src/* .
RUN chmod +x /config/start.sh; \
    cp /config/php.ini /etc/php84/php.ini && \
    cp /config/php_fpm_site.conf /etc/php84/php-fpm.d/www.conf; \
    cp /config/nginx_site.conf /etc/nginx/http.d/default.conf; \
    chown nobody:nginx /var/www/html/* -R;

EXPOSE 8080
ENTRYPOINT ["/config/start.sh"]
CMD ["nginx", "-g", "daemon off;"]

## Health Check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s \
  CMD curl -f http://127.0.0.1:8080 || exit 1
