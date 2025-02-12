#!/usr/bin/env bash
  for ARGS in $@; do
	    case $ARGS in    
	        "install")     cd ./scripts; npm run installation;;
	    esac
  done
    if [[ -z $@ ]]; then
        php-fpm7.4 -O -F --fpm-config ./scripts/config/php-fpm.conf &
        cd ./scripts
        npm start
    fi
