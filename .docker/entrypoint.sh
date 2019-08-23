#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
composer install
php artisan key:generate
php artisan migrate
chown -R www-data:1000 /var/www
chown -R 775 /var/www
php-fpm
