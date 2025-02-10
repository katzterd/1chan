FROM node:20.6.0 AS builder

WORKDIR /npm

ADD ./scripts/package.json ./

RUN npm install



FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y \
        ca-certificates \
        curl \
        gnupg

RUN mkdir -p /etc/apt/keyrings

RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | \
gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg

RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | \
tee /etc/apt/sources.list.d/nodesource.list

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        sphinxsearch \
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
        nodejs

WORKDIR /1chan

ADD ./app                   ./app
ADD ./resources             ./resources
ADD ./www                   ./www
ADD ./scripts               ./scripts
ADD ./instance-config.php   ./instance-config.php

RUN mv ./resources/smilies    ./www/img/smilies
RUN mv ./resources/homeboards ./www/ico/homeboards

RUN mkdir -p /var/lib/sphinxsearch

COPY --from=builder /npm/node_modules ./scripts/node_modules

EXPOSE 80

ENTRYPOINT ["nginx" "-c" "/1chan/scripts/config/nginx.conf" "&" "/1chan/scripts/config/docker-entrypoint.sh"]