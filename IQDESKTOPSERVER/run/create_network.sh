#!/bin/bash

# Define network name (same as in yml template)
NETWORK=iqdesktop

docker network ls | grep $NETWORK > check
CONTENT=$(<check)
rm -f check

if [[ -n $CONTENT ]]; then
  echo "Network $NETWORK already created - doing nothing"
else
  echo "Creating network $NETWORK ..."
  docker network create \
  --driver=bridge \
  --subnet=172.28.0.0/16 \
  --ip-range=172.28.5.0/24 \
  --gateway=172.28.5.254 \
  $NETWORK
fi

