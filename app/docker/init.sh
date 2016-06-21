#!/bin/bash

echo "Setting up permissions to www-data ..."
usermod -u 1000 www-data

echo "Starting PHP-fpm ..."
service php7.1-fpm start

echo "Installing composer dependencies ..."
cd /vagrant
php /composer.phar install

echo "Starting Nginx ..."
nginx
