#!/usr/bin/env bash

for ARGS in $@; do
	    case $ARGS in    
	        "getaddr")     echo "Your yggdrasil address is: http://[$(yggdrasilctl getSelf | grep "IPv6 address" | cut -d ' ' -f '8' | xargs)]";;
	    esac
  done


if [[ -z $@ ]]; then

    if [ -z "${YGGDRASILGATE_ENDPOINT_HOST}" ]; then
        echo "YGGDRASILGATE_ENDPOINT_HOST environment var is undefined, yggdrasil will be disabled";
        exit 0;
    else
        sed -i 's/\__YGGDRASILGATE_ENDPOINT_HOST__/'"${YGGDRASILGATE_ENDPOINT_HOST}"'/g' ./supervisord.conf
    fi
    
    if [ -z "${YGGDRASILGATE_ENDPOINT_PORT}" ]; then
        echo "YGGDRASILGATE_ENDPOINT_PORT environment var is undefined, yggdrasil will be disabled";
        exit 0;
    else
        sed -i 's/\__YGGDRASILGATE_ENDPOINT_PORT__/'"${YGGDRASILGATE_ENDPOINT_PORT}"'/g' ./supervisord.conf
    fi

    if [ -z "${YGGDRASILGATE_PRIVATE_KEY}" ]; then
        echo "YGGDRASILGATE_PRIVATE_KEY environment var is undefined, yggdrasil will be disabled";
        exit 0;
    else
        sed -i 's/__YGGDRASILGATE_PRIVATE_KEY__/'"PrivateKey: ${YGGDRASILGATE_PRIVATE_KEY}"'/g' ./yggdrasil.conf
        printf "Updating peers...\n\n"
        ./peers_updater -c ./yggdrasil.conf -n 5 -u
        printf "yggdrasil started: ${YGGDRASILGATE_ENDPOINT_HOST}:${YGGDRASILGATE_ENDPOINT_PORT}\n\n"
        supervisord -c ./supervisord.conf
    fi

fi
