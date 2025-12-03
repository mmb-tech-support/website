#!/bin/sh

set -eu

# Read secrets from Docker Swarm secrets if available

if [ -f /run/secrets/db_name ]; then
  DB_NAME=$(cat /run/secrets/db_name)
  export DB_NAME
else
  echo "Secret db_name not found." >&2
  exit 1
fi

if [ -f /run/secrets/db_user ]; then
  DB_USER=$(cat /run/secrets/db_user)
  export DB_USER
else
  echo "Secret db_user not found." >&2
  exit 1
fi

if [ -f /run/secrets/db_password ]; then
  DB_PASSWORD=$(cat /run/secrets/db_password)
  export DB_PASSWORD
else
  echo "Secret db_password not found." >&2
  exit 1
fi

/usr/local/bin/docker-php-entrypoint php-fpm