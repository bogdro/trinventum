#!/bin/bash
#
# Trinventum Docker configuration - container build script
#
# Copyright (C) 2024-2026 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
#
# This file is part of Trinventum (Transaction and Inventory Unified Manager),
#  a software that helps manage an e-commerce business.
# Trinventum homepage: https://trinventum.sourceforge.io/
#
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  You should have received a copy of the GNU Affero General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

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
docker run -d -p 80:80 -p 443:443 --name trin-app --network trinventum_net --hostname trinventum-www --sysctl net.ipv4.ip_unprivileged_port_start=0 trinventum-app:latest

sed -i "s/POSTGRES_PASSWORD=.*/POSTGRES_PASSWORD=$pg_master_pwd/" docker/docker-compose.yaml
sed -i "s/PGPWD=.*/PGPWD=$pg_trinventum_pwd/" docker/docker-compose.yaml
#docker-compose -p trinventum -f docker/docker-compose.yaml up -d

##docker stop trin-app
##docker rm trin-app
##docker stop trin-db
##docker rm trin-db

##docker network rm trinventum_net
