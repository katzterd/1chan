#!/usr/bin/env bash
/usr/sbin/nginx -c /src/nginx.conf > /dev/stdout & \
php-fpm7.4 -O -F --fpm-config /src/php-fpm.conf
