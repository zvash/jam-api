#!/usr/bin/env bash

while [[ true ]]

do

php /var/www/milanjam/artisan schedule:run --verbose --no-interaction &

sleep 30

done
