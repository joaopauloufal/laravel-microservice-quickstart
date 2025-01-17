#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh

cp .env.example .env
cp .env.testing.example .env.testing
composer install
php artisan key:generate
php artisan migrate --seed
chown -R www-data:1000 /var/www/storage /var/www/public /var/www/app
chmod -R 775 /var/www/storage /var/www/public /var/www/app
php-fpm
