#!/usr/bin/env bash

if [ -z "${I2PGATE_PRIVATE_KEY}" ]; then
    echo "I2PGATE_PRIVATE_KEY environment var is undefined, i2pgate will be disabled";
    exit 0;
else
    echo "${I2PGATE_PRIVATE_KEY}" | base64 -d >> /var/lib/i2pd/secret_key.dat
fi

if [ -z "${I2PGATE_ENDPOINT}" ]; then
    echo "I2PGATE_ENDPOINT environment var is undefined, i2pgate will be disabled";
    exit 0;
else
    sed -i 's/\__I2PGATE_ENDPOINT__/'"${I2PGATE_ENDPOINT}"'/' /etc/i2pd/tunnels.conf
fi

printf "i2pgate started: http://${I2PGATE_ENDPOINT}:80\n\n"

i2pd --service --conf /etc/i2pd/i2pd.conf
