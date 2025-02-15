#!/usr/bin/env bash
  for ARGS in $@; do
	    case $ARGS in    
	        "install")     npm run installation;;
	    esac
  done
    if [[ -z $@ ]]; then
        npm start
    fi
