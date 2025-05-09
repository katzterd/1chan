FROM ubuntu:22.04

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y tor curl

ADD ./docker/config/torgate/init.sh           /init.sh
ADD ./docker/config/torgate/torrc.conf        /etc/tor/torrc

CMD [ "bash", "/init.sh" ]
