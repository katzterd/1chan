#!/usr/bin/env bash

if [ -z "${I2PGATE_PRIVATE_KEY}" ]; then
    echo "I2PGATE_PRIVATE_KEY environment var is undefined, i2pgate will be disabled";
    exit 0;
else
    echo "${I2PGATE_PRIVATE_KEY}" | base64 -d >> /var/lib/i2pd/secret_key.dat
fi

if [ -z "${I2PGATE_ENDPOINT_HOST}" ]; then
    echo "I2PGATE_ENDPOINT_HOST environment var is undefined, i2pgate will be disabled";
    exit 0;
else
    sed -i 's/\__I2PGATE_ENDPOINT_HOST__/'"${I2PGATE_ENDPOINT_HOST}"'/' /etc/i2pd/tunnels.conf
fi

if [ -z "${I2PGATE_ENDPOINT_PORT}" ]; then
    echo "I2PGATE_ENDPOINT_PORT environment var is undefined, i2pgate will be disabled";
    exit 0;
else
    sed -i 's/\__I2PGATE_ENDPOINT_PORT__/'"${I2PGATE_ENDPOINT_PORT}"'/' /etc/i2pd/tunnels.conf
fi

printf "i2pgate started: ${I2PGATE_ENDPOINT_HOST}:${I2PGATE_ENDPOINT_PORT}\n\n"

i2pd --service --conf /etc/i2pd/i2pd.conf
