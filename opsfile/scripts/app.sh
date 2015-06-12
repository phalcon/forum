#!/usr/bin/env bash
#cp app/config/config.example.php app/config/config.php
cd /usr/share/nginx/html/www
composer install --no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader

chmod 777 -R /usr/share/nginx/html/www/app/logs
chmod 777 -R /usr/share/nginx/html/www/app/cache