#!/bin/bash
cd /sites/MilanJam/milan-jam-api
#sudo rm -rf error_log
#sudo rm -rf access_log
sudo composer install -n && sudo composer dump-autoload -n
sudo chmod -R 0777 storage bootstrap/cache
sudo docker exec -it milanjam php artisan storage:link
sudo chown admin:admin /sites/MilanJam/milan-jam-api -R
sudo php artisan optimize:clear
sudo php artisan cache:clear
sudo php artisan view:clear
sudo php artisan route:cache
sudo php artisan event:cache
sudo php artisan config:cache
sudo docker exec -it milanjam php artisan migrate --force
sudo chmod 600 storage/oauth-private.key
sudo php artisan optimize:clear
