#!/usr/bin/env bash

./run-scheduler.sh &

docker-php-entrypoint php-fpm
