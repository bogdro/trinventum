#!/bin/bash

docker build --pull -t trinventum-app -f docker/Dockerfile-trinventum-app .
docker build --pull -t trinventum-db -f docker/Dockerfile-trinventum-db .

docker network create trinventum_net

echo -n "PostgreSQL new master password: "
# e.g. postgres1234
read -s pg_master_pwd
echo ''

echo -n "PostgreSQL new 'trinventum' password: "
# e.g. trinventum01
read -s pg_trinventum_pwd
echo ''

# NOTE: the network must be the same; set some reasonable hostname
docker run -d --name trin-db --network trinventum_net --hostname trinventum-db -e POSTGRES_PASSWORD="$pg_master_pwd" -e PGPWD="$pg_trinventum_pwd" trinventum-db:latest
docker run -d -p 80:80 -p 443:443 --name trin-app --network trinventum_net --hostname trinventum-www trinventum-app:latest

sed -i "s/POSTGRES_PASSWORD=.*/POSTGRES_PASSWORD=$pg_master_pwd/" docker/docker-compose.yaml
sed -i "s/PGPWD=.*/PGPWD=$pg_trinventum_pwd/" docker/docker-compose.yaml
#docker-compose -p trinventum -f docker/docker-compose.yaml up -d

##docker stop trin-app
##docker rm trin-app
##docker stop trin-db
##docker rm trin-db

##docker network rm trinventum_net
