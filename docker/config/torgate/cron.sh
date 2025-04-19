#!/usr/bin/env bash

if [ -z "${TORGATE_HOSTNAME}" ]; then
    echo "TORGATE_HOSTNAME environment var is undefined, cronjob will be disabled";
    exit 0;
else
    echo "Waiting 60 seconds before circuit warmup...";
    sleep 60 && \
    curl -sS --socks5-hostname localhost:9050 http://${TORGATE_HOSTNAME}/lifecheck && \
    echo "Warmed up! Next warmup will execute at $(date -d "now + 8 hours + 1 minute" +"%Y-%m-%d %H:%M:%S")" && \
    sleep 28800;
fi
