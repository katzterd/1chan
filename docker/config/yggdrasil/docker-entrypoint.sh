#!/usr/bin/env bash

for ARGS in $@; do
	    case $ARGS in    
	        "getaddr")     echo "Your yggdrasil address is: http://[$(yggdrasilctl getSelf | grep "IPv6 address" | cut -d ' ' -f '8' | xargs)]";;
	    esac
  done


if [[ -z $@ ]]; then

    if [ -z "${YGGDRASILGATE_ENDPOINT}" ]; then
        echo "YGGDRASILGATE_ENDPOINT environment var is undefined, yggdrasil will be disabled";
        exit 0;
    else
        sed -i 's/\__YGGDRASILGATE_ENDPOINT__/'"${YGGDRASILGATE_ENDPOINT}"'/g' /ygg/supervisord.conf
    fi

    if [ -z "${YGGDRASILGATE_PRIVATE_KEY}" ]; then
        echo "YGGDRASILGATE_PRIVATE_KEY environment var is undefined, yggdrasil will be disabled";
        exit 0;
    else
        sed -i 's/__YGGDRASILGATE_PRIVATE_KEY__/'"PrivateKey: ${YGGDRASILGATE_PRIVATE_KEY}"'/g' /ygg/yggdrasil.conf
        printf "Updating peers...\n\n"
        ./peers_updater -c /ygg/yggdrasil.conf -n 5 -u
        printf "yggdrasil started\n\n"
        supervisord -c /ygg/supervisord.conf
    fi

fi
