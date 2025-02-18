FROM node:20.6.0 AS builder

WORKDIR /npm

ADD ./scripts/package.json ./

RUN npm install


FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        gnupg

RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | \
  gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
  echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | \
  tee /etc/apt/sources.list.d/nodesource.list

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        sphinxsearch \
        nodejs
        
RUN mkdir -p /var/lib/sphinxsearch

WORKDIR /src

ADD ./docker/config/service           /
ADD ./scripts                         ./scripts
COPY --from=builder /npm/node_modules ./scripts/node_modules
ADD ./resources                       ./resources

RUN mkdir -p ./www/img && \
    mkdir -p ./www/ico

WORKDIR /src/scripts

VOLUME /var/lib/sphinxsearch

EXPOSE 9393
EXPOSE 3312

ENTRYPOINT [ "/bin/bash", "-c", "/docker-entrypoint.sh" ]
