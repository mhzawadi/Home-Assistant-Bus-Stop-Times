#!/bin/sh

if [ "$apiToken" != "" ]
then
  sed -i "s!\$apiToken = \"\";!\$apiToken = \"$apiToken\";!" /var/www/html/config.php
else
  echo "You need an API key"
  exit
fi
if [ "$endpoint" != "" ]
then
  sed -i "s!\$endpoint = \"https://reading-opendata.r2p.com/api/v1\";!\$endpoint = \"https://$endpoint-opendata.r2p.com/api/v1\";!" /var/www/html/config.php
fi
if [ "$stop" != "" ]
then
  sed -i "s!\$stop = \"\";!\$stop = \"$stop\";!" /var/www/html/config.php
fi
if [ "$lines" != "" ]
then
  sed -i "s!\$lines = \"\";!\$lines = \"$lines\";!" /var/www/html/config.php
fi


if [ -n "$allow_php_status_ip" ]
then
  sed -i -e "s!127.0.0.2!$allow_php_status_ip!" /etc/nginx/http.d/default.conf
fi

if [ -n "$allow_php_ping_ip" ]
then
  sed -i -e "s!127.0.0.3!$allow_php_ping_ip!" /etc/nginx/http.d/default.conf
fi

if [ -n "$php_ping_text" ]
then
  sed -e "s/pong/$php_ping_text/" /config/php_fpm_site.conf > /etc/php84/php-fpm.d/www.conf
fi

if [ -n "$php_timezone" ]
then
  sed -i'' "s!date.timezone = \"US/Central\"!date.timezone = \"$php_timezone\"!" /etc/php84/php.ini
fi

ln -s /dev/stdout /var/log/fpm-php.www.log
ln -s /dev/stdout /var/log/nginx/access.log

php-fpm84

exec "$@"
