#!/usr/bin/env bash
# Atualiza o ARS no EUROPA-HD com o que estiver no GitHub.
# Uso (no servidor): cd /var/www/ars && ./deploy/atualizar.sh
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"
umask 002

BRANCH="$(git rev-parse --abbrev-ref HEAD)"

echo "== 1/6 Backup"
./deploy/backup.sh

# Manutencao antes de trocar o codigo: ninguem usa o codigo novo com as dependencias antigas.
php artisan down --retry=30 || true
trap 'php artisan up' EXIT

echo "== 2/6 Codigo ($BRANCH)"
ANTES="$(git rev-parse --short HEAD)"
git fetch --quiet origin "$BRANCH"
git merge --ff-only --quiet "origin/$BRANCH"
echo "   $ANTES -> $(git rev-parse --short HEAD)"

echo "== 3/6 Dependencias PHP"
composer install --no-dev --no-interaction --no-progress --optimize-autoloader

echo "== 4/6 Front (Vite)"
npm ci --no-audit --no-fund --loglevel=error
npm run build --silent

echo "== 5/6 Banco (migrations; nunca --seed em producao)"
php artisan migrate --force

echo "== 6/6 Caches"
php artisan optimize:clear >/dev/null
php artisan optimize
sudo systemctl reload php8.5-fpm

echo "Atualizado. Confira em $(sed -n 's/^APP_URL=//p' .env | tr -d '"')"
