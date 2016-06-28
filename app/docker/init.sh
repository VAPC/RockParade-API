#!/bin/bash

echo "Starting PHP-fpm ..."
service php7.1-fpm start

echo "Starting MySQL ..."
service mysql start

echo "Installing composer dependencies ..."
cd /vagrant
php /home/vagrant/composer.phar install

echo "Starting Nginx ..."
service nginx start
