FROM ubuntu:22.04

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y tor curl supervisor

ADD ./docker/config/torgate/supervisord.conf  /supervisord.conf
ADD ./docker/config/torgate/cron.sh           /cron.sh
ADD ./docker/config/torgate/init.sh           /init.sh
ADD ./docker/config/torgate/torrc.conf        /etc/tor/torrc

CMD [ "supervisord" "-c" "/supervisord.conf" ]
