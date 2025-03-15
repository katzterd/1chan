FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive
ARG CSS_PATH=www/css/themes

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        supervisor \
        nginx \
        imagemagick \
        php7.4 \
        php7.4-fpm \
        php7.4-cli \
        php7.4-common \
        php7.4-curl \
        php7.4-mbstring \
        php7.4-mysql \
        php7.4-xml \
        php7.4-gd \
        php7.4-zip \
        php7.4-bcmath \
        php7.4-redis \
        php7.4-imagick \
        sphinxsearch

WORKDIR /src

ADD ./docker/config/1chan   /
ADD ./app                   ./app
ADD ./www                   ./www

RUN bash -c 'for example in $CSS_PATH/*.example ; do mv $example ${example//css.example/css} ; done'

EXPOSE 80

ENTRYPOINT [ "/bin/bash", "-c", "/docker-entrypoint.sh" ]
