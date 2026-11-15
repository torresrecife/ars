#!/usr/bin/env bash
# Backup da ARS: banco (REIA-HD) em /var/backups/ars. Mantem 14 dias.
# A ARS nao guarda arquivos enviados; os dados do NEO ficam no SQL Server.
# Uso: deploy/backup.sh  (o atualizar.sh chama antes de cada atualizacao)
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/ars}"
KEEP_DAYS="${KEEP_DAYS:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"

# Le uma variavel do .env (sem aspas em volta).
env_value() {
    sed -n "s/^$1=//p" "$APP_DIR/.env" | tail -n 1 | sed -e 's/^"\(.*\)"$/\1/' -e "s/^'\(.*\)'$/\1/"
}

mkdir -p "$BACKUP_DIR"
umask 077

DB_HOST="$(env_value DB_HOST)"
DB_PORT="$(env_value DB_PORT)"
DB_DATABASE="$(env_value DB_DATABASE)"
DB_USERNAME="$(env_value DB_USERNAME)"

echo "Banco $DB_DATABASE em $DB_HOST..."
MYSQL_PWD="$(env_value DB_PASSWORD)" mysqldump \
    --host="$DB_HOST" --port="${DB_PORT:-3306}" --user="$DB_USERNAME" \
    --single-transaction --quick --routines --triggers --no-tablespaces --set-gtid-purged=OFF \
    --default-character-set=utf8mb4 \
    "$DB_DATABASE" | gzip > "$BACKUP_DIR/banco-$STAMP.sql.gz.tmp"
mv "$BACKUP_DIR/banco-$STAMP.sql.gz.tmp" "$BACKUP_DIR/banco-$STAMP.sql.gz"

find "$BACKUP_DIR" -maxdepth 1 -type f -name 'banco-*.sql.gz' -mtime +"$KEEP_DAYS" -delete

echo "Backup pronto: $BACKUP_DIR/banco-$STAMP.sql.gz"
