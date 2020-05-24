#!/usr/bin/env bash

if [ -f "./tester-backend/geckodriver.log" ]; then
  rm "./tester-backend/geckodriver.log";
fi

mkdir "./tester-backend/resources";
mkdir "./tester-backend/resources/addons";
mkdir "./tester-backend/resources/addons/unziped";

cd tester-gui || exit;

composer install

cd ../

docker-compose down --rmi all
docker-compose rm -f
docker-compose build --no-cache
docker-compose up -d

docker exec -i db mysql -e "DROP DATABASE IF EXISTS laravel;"

docker exec -i db mysql --defaults-extra-file=/etc/mysql/my.cnf < mysqldump.sql

NGINX_IP=$(docker exec -i backend getent hosts nginx | awk '{ print $1 ; exit }')
docker exec -i backend sh -c "cat >> /etc/hosts <<EOF
$NGINX_IP www.youtube.com
$NGINX_IP www.facebook.com
$NGINX_IP twitter.com

EOF"

cat > ./tester-gui/.env ./tester-gui/docker/.env
docker exec -i app sh -c "cat >> .env <<EOF
NGINX_IP=$NGINX_IP

EOF"