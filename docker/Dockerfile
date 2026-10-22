FROM alpine:3.24
LABEL org.opencontainers.image.authors="matt@horwood.biz"

# Install required deb packages
RUN apk update && apk upgrade && \
    apk add gnupg nginx php85-fpm php85-simplexml php85-xmlwriter php85-tokenizer \
    php85-curl php85 php85-phar curl git \
    && rm -f /var/cache/apk/* \
    && mkdir -p /var/www/html/ \
    && mkdir -p /run/nginx; \
    [ -f /usr/bin/php ] && rm -f /usr/bin/php; \
    ln -s /usr/bin/php83 /usr/bin/php;

COPY docker/config /config

WORKDIR /var/www/html
COPY src/* .
RUN chmod +x /config/start.sh; \
    cp /config/php.ini /etc/php85/php.ini && \
    cp /config/php_fpm_site.conf /etc/php85/php-fpm.d/www.conf; \
    cp /config/nginx_site.conf /etc/nginx/http.d/default.conf; \
    chown nobody:nginx /var/www/html/* -R;

EXPOSE 8080
ENTRYPOINT ["/config/start.sh"]
CMD ["nginx", "-g", "daemon off;"]

## Health Check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s \
  CMD curl -f http://127.0.0.1:80 || exit 1
