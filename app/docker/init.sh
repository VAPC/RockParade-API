#!/bin/bash

echo "Setting up permissions to www-data ..."
usermod -u 1000 www-data
sudo chown -R www-data /vagrant/vendor
sudo chown -R www-data /vagrant/bin

echo "Starting PHP-fpm ..."
service php7.1-fpm start

echo "Starting MySQL ..."
service mysql start

echo "Installing composer dependencies ..."
cd /vagrant
sudo -u www-data php /composer.phar install

echo "Starting Nginx ..."
nginx
