#!/bin/sh

set -eu

BACKUP_DIR="/backups"
FULL_DIR="${BACKUP_DIR}/full"

if [ ! -f "${FULL_DIR}/base-backup.cnf" ]; then
    mkdir -p "${FULL_DIR}"

    printf "Full backup...\n"
    mariabackup \
        --backup \
        --host="${DB_HOST}" \
        --user=root \
        --password="${DB_ROOT_PASSWORD}" \
        --target-dir="${FULL_DIR}"
    mariabackup \
        --prepare \
        --target-dir="${FULL_DIR}"
else
    printf "Incremental backup\n"

    TIMESTAMP=$(date +%F_%H-%M-%S)
    INC_DIR="${BACKUP_DIR}/inc/${TIMESTAMP}"
    mkdir -p "${INC_DIR}"

    mariabackup \
        --backup \
        --host="${DB_HOST}" \
        --user=root \
        --password="${DB_ROOT_PASSWORD}" \
        --target-dir="${INC_DIR}" \
        --incremental-basedir="${FULL_DIR}"
    mariabackup \
        --prepare \
        --target-dir="${FULL_DIR}" \
        --incremental-dir="${INC_DIR}"
fi

echo "Backup completed"
