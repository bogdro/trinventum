#!/bin/bash
set -e

#psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
psql -v --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE USER trinventum WITH PASSWORD '$PGPWD';
	CREATE DATABASE trinventum WITH OWNER trinventum;
	GRANT ALL PRIVILEGES ON DATABASE trinventum TO trinventum;
EOSQL
