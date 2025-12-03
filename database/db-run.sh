#!/bin/sh

set -eu

# Read secrets from Docker Swarm secrets if available

if [ -f /run/secrets/db_name ]; then
  MARIADB_DATABASE=$(cat /run/secrets/db_name)
  export MARIADB_DATABASE
else
  echo "Secret db_name not found." >&2
  exit 1
fi

if [ -f /run/secrets/db_root_password ]; then
  MARIADB_ROOT_PASSWORD=$(cat /run/secrets/db_root_password)
  export MARIADB_ROOT_PASSWORD
else
  echo "Secret db_root_password not found." >&2
  exit 1
fi

/usr/local/bin/docker-entrypoint.sh mariadbd