FROM node:20.6.0 AS builder

WORKDIR /npm

ADD ./scripts/package.json ./

RUN npm install


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
        sphinxsearch \
        ca-certificates \
        curl \
        gnupg

RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | \
  gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
  echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | \
  tee /etc/apt/sources.list.d/nodesource.list
  
RUN apt-get update && \
    apt-get install -y --no-install-recommends nodejs
    
RUN mkdir -p /var/lib/sphinxsearch

WORKDIR /src

ADD ./config                          /
ADD ./scripts                         ./scripts
COPY --from=builder /npm/node_modules ./scripts/node_modules
ADD ./resources                       ./resources
ADD ./app                             ./app
ADD ./www                             ./www

RUN bash -c 'for example in $CSS_PATH/*.example ; do mv $example ${example//css.example/css} ; done'

VOLUME /src/www/img/smilies
VOLUME /src/www/ico/homeboards
VOLUME /src/www/uploads
VOLUME /var/lib/sphinxsearch

EXPOSE 80

ENTRYPOINT [ "bash", "/docker-entrypoint.sh" ]
