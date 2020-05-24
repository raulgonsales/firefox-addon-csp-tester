#!/usr/bin/env bash

if [ -f "./tester-backend/geckodriver.log" ]; then
  rm "./tester-backend/geckodriver.log";
fi

#prepare directory structure for addons downloading
mkdir "./tester-backend/resources";
mkdir "./tester-backend/resources/addons";
mkdir "./tester-backend/resources/addons/unziped";

cd tester-gui || exit;

#install all laravel dependencies
composer install

cd ../

#build and run Docker containers
docker-compose down --rmi all
docker-compose rm -f
docker-compose build --no-cache
docker-compose up -d

#prepare database
docker exec -i db mysql -e "DROP DATABASE IF EXISTS laravel; CREATE DATABASE laravel;"

#prepare tables and seed some initial data
docker exec -i app php artisan migrate
docker exec -i app php artisan db:seed

#get IP address of 'nginx' container
NGINX_IP=$(docker exec -i backend getent hosts nginx | awk '{ print $1 ; exit }')
#make DNS faking
docker exec -i backend sh -c "cat >> /etc/hosts <<EOF
$NGINX_IP www.youtube.com
$NGINX_IP www.facebook.com
$NGINX_IP twitter.com

EOF"

#use the IP for other purposes in the application by putting it into env variables
cat > ./tester-gui/.env ./tester-gui/docker/.env
#override environment variables
docker exec -i app sh -c "cat >> .env <<EOF
NGINX_IP=$NGINX_IP

EOF"