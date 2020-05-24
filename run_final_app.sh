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

