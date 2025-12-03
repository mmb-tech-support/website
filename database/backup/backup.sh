#!/bin/sh

set -eu

if [ -f /run/secrets/db_backup_user ]; then
  MARIADB_USER=$(cat /run/secrets/db_backup_user)
else
  printf "[%s] Secret db_backup_user not found.\n" "$(date '+%Y-%m-%d %H:%M:%S')" >&2
  exit 1
fi

if [ -f /run/secrets/db_password ]; then
  MARIADB_PASSWORD=$(cat /run/secrets/db_password)
else
  printf "[%s] Secret db_password not found.\n" "$(date '+%Y-%m-%d %H:%M:%S')" >&2
  exit 1
fi

BACKUP_MODE="${BACKUP_MODE:-AUTO}"

BACKUP_DIR="/backups"
FULL_DIR="${BACKUP_DIR}/full"
INC_DIR_BASE="${BACKUP_DIR}/inc"

do_restore() {
  BACKUP_DIR_RESTORE="${BACKUP_DIR}/restore"
  TARGET_DIR="/var/lib/mysql"
  printf "[%s] Restoring from %s to %s\n" "$(date '+%Y-%m-%d %H:%M:%S')" "$BACKUP_DIR_RESTORE" "$TARGET_DIR"

  # Verify backup by running prepare step
  printf "[%s] Verifying backup with mariadb-backup --prepare...\n" "$(date '+%Y-%m-%d %H:%M:%S')"
  mariadb-backup --prepare --target-dir="$BACKUP_DIR_RESTORE"
  if [ $? -ne 0 ]; then
    printf "[%s] ERROR: mariadb-backup prepare failed!\n" "$(date '+%Y-%m-%d %H:%M:%S')" >&2
    exit 1
  fi
  find $TARGET_DIR -mindepth 1 -delete 
  mariadb-backup --copy-back --target-dir="$BACKUP_DIR_RESTORE" --datadir="$TARGET_DIR"
  chown -R mysql:mysql "$TARGET_DIR"
  printf "[%s] Restore completed\n" "$(date '+%Y-%m-%d %H:%M:%S')"
}

do_full_backup() {
  printf "[%s] Performing full backup...\n" "$(date '+%Y-%m-%d %H:%M:%S')"
  FULL_DIR_NEW="${FULL_DIR}_new_$(date +%F_%H-%M-%S)"
  mkdir -p "${FULL_DIR_NEW}"
  mariadb-backup \
    --backup \
    --host="${DB_HOST}" \
    --user="${MARIADB_USER}" \
    --password="${MARIADB_PASSWORD}" \
    --target-dir="${FULL_DIR_NEW}"
  if [ $? -ne 0 ]; then
    printf "[%s] ERROR: Full backup failed!\n" "$(date '+%Y-%m-%d %H:%M:%S')" >&2
    rm -rf "${FULL_DIR_NEW}"
    exit 1
  fi
  mariadb-backup \
    --prepare \
    --target-dir="${FULL_DIR_NEW}"
  if [ $? -ne 0 ]; then
    printf "[%s] ERROR: Prepare full backup failed!\n" "$(date '+%Y-%m-%d %H:%M:%S')" >&2
    rm -rf "${FULL_DIR_NEW}"
    exit 1
  fi
  # Only now remove old backups
  rm -rf "${FULL_DIR}" "${INC_DIR_BASE}" 2>/dev/null || true
  if [ -d "${FULL_DIR_NEW}" ]; then
    mv "${FULL_DIR_NEW}" "${FULL_DIR}"
  else
    printf "[%s] ERROR: New backup directory not found for move!\n" "$(date '+%Y-%m-%d %H:%M:%S')" >&2
    exit 1
  fi
  printf "[%s] Full backup completed. Old full/incrementals removed.\n" "$(date '+%Y-%m-%d %H:%M:%S')"
}

do_incremental_backup() {
  if [ ! -f "${FULL_DIR}/backup-my.cnf" ]; then
    printf "[%s] No full backup found. Performing full backup first.\n" "$(date '+%Y-%m-%d %H:%M:%S')"
    do_full_backup
    return
  fi
  printf "[%s] Performing incremental backup...\n" "$(date '+%Y-%m-%d %H:%M:%S')"
  TIMESTAMP=$(date +%F_%H-%M-%S)
  INC_DIR="${INC_DIR_BASE}/${TIMESTAMP}"
  mkdir -p "${INC_DIR}"
  mariadb-backup \
    --backup \
    --host="${DB_HOST}" \
    --user="${MARIADB_USER}" \
    --password="${MARIADB_PASSWORD}" \
    --target-dir="${INC_DIR}" \
    --incremental-basedir="${FULL_DIR}"
  mariadb-backup \
    --prepare \
    --target-dir="${FULL_DIR}" \
    --incremental-dir="${INC_DIR}"
  printf "[%s] Incremental backup completed.\n" "$(date '+%Y-%m-%d %H:%M:%S')"
}


case "$BACKUP_MODE" in
  FULL)
    do_full_backup
    printf "[%s] Backup completed\n" "$(date '+%Y-%m-%d %H:%M:%S')"
    ;;
  INCREMENTAL)
    do_incremental_backup
    printf "[%s] Backup completed\n" "$(date '+%Y-%m-%d %H:%M:%S')"
    ;;
  RESTORE)
    do_restore
    printf "[%s] Restore completed\n" "$(date '+%Y-%m-%d %H:%M:%S')"
    ;;
  AUTO|*)
    do_incremental_backup
    printf "[%s] Backup completed\n" "$(date '+%Y-%m-%d %H:%M:%S')"
    ;;
esac
