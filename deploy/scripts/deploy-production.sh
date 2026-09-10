#!/bin/bash
# =============================================================
# Micrositio OICM — Despliegue a PRODUCCIÓN
# =============================================================
# USO:
#   bash /srv/munioax/oicm/deploy/scripts/deploy-production.sh
#
# QUÉ HACE:
#   1. Pausa de 5s para cancelar
#   2. Verifica que MySQL (compose separado) y su red están arriba
#   3. Libera espacio en disco
#   4. Actualiza el código desde la rama 'main'
#   5. Reconstruye la imagen y reinicia SOLO la app
#   6. Migra siempre; SIEMBRA SOLO en el primer deploy — NUNCA borra ni
#      resetea datos editados desde el panel
#   7. Regenera cachés de Laravel
#
# ⚠️ A diferencia de otros portales del municipio, aquí `db:seed` NO se
# repite en cada deploy (ver deploy-staging.sh para el detalle: los
# seeders de este proyecto tienen contraseña de prueba fija y datos de
# catálogo/ejemplo, no son idempotentes frente a ediciones del panel).
# El script deja un marcador storage-data/.seeded tras la primera siembra
# y la omite después. Para forzar una re-siembra deliberada, borra ese
# marcador a mano.
#
# REQUISITOS:
#   - MySQL corriendo:  docker compose -f deploy/docker-compose.mysql.yml up -d
#   - Red 'oicm_network' creada (la crea el compose de MySQL)
#   - .env configurado en REPO_PATH/.env (usa .env.production.example)
# =============================================================

set -euo pipefail

REPO_PATH="/srv/munioax/oicm"
COMPOSE_FILE="deploy/docker-compose.production.yml"
MYSQL_COMPOSE="deploy/docker-compose.mysql.yml"
ENV_FILE="$REPO_PATH/.env"
SEEDED_MARKER="$REPO_PATH/storage-data/.seeded"
BRANCH="main"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
log()  { echo -e "${GREEN}  ✓ $1${NC}"; }
info() { echo -e "${BLUE}  → $1${NC}"; }
warn() { echo -e "${YELLOW}  ⚠ $1${NC}"; }
err()  { echo -e "${RED}  ✗ ERROR: $1${NC}" >&2; exit 1; }

dc() { docker compose --env-file "$ENV_FILE" -f "$COMPOSE_FILE" "$@"; }

echo ""
echo -e "${RED}========================================================"
echo "   Micrositio OICM — Despliegue a PRODUCCIÓN"
echo "   $(date '+%d/%m/%Y %H:%M:%S')"
echo -e "========================================================${NC}"
warn "Vas a desplegar a PRODUCCIÓN. Ctrl+C en 5 segundos para cancelar."
sleep 5
echo ""

info "Verificando requisitos..."
[ -d "$REPO_PATH" ]      || err "No existe el directorio $REPO_PATH"
[ -d "$REPO_PATH/.git" ] || err "$REPO_PATH no es un repositorio git"
[ -f "$ENV_FILE" ]       || err "No existe $ENV_FILE — créalo desde .env.production.example"
command -v docker &>/dev/null      || err "Docker no está instalado"
docker compose version &>/dev/null || err "Docker Compose v2 no está instalado"

docker network inspect oicm_network >/dev/null 2>&1 || \
    err "La red 'oicm_network' no existe.\n  Primero: docker compose --env-file $ENV_FILE -f $MYSQL_COMPOSE up -d"
docker ps --filter "name=oicm_mysql" --filter "status=running" | grep -q oicm_mysql || \
    err "MySQL 'oicm_mysql' no está corriendo.\n  Ejecuta: docker compose --env-file $ENV_FILE -f $MYSQL_COMPOSE up -d"
log "Requisitos verificados (MySQL y red arriba)"

info "Liberando espacio en disco..."
# OJO: NO usar 'docker container prune -f' ni 'docker image prune -f' sin
# filtro: son host-wide y borran contenedores/imágenes de los OTROS portales
# del servidor. Solo la caché de build, que es regenerable — segura de purgar.
docker builder prune -f 2>/dev/null || true
log "Espacio liberado — disponible: $(df -h / | awk 'NR==2{print $4}')"

cd "$REPO_PATH"
git config --global --add safe.directory "$REPO_PATH" 2>/dev/null || true

info "Actualizando código desde GitHub (rama: $BRANCH)..."
git fetch origin
git checkout "$BRANCH"
git pull origin "$BRANCH"
log "Código actualizado — commit: $(git rev-parse --short HEAD)"

info "Preparando storage persistente (bind mount)..."
mkdir -p "$REPO_PATH"/storage-data/{app/public,framework/cache/data,framework/sessions,framework/views,logs}
sudo chown -R 33:33 "$REPO_PATH/storage-data"
log "Storage listo"

info "Construyendo imagen Docker..."
dc build
log "Imagen construida"

info "Reiniciando la app..."
dc up -d --remove-orphans
log "App iniciada"

info "Esperando a que la app arranque..."
sleep 10

# Config fresca antes de migrar/sembrar.
dc exec -T app php artisan config:clear

info "Ejecutando migraciones..."
dc exec -T app php artisan migrate --force
log "Migraciones ejecutadas"

# Siembra SOLO en el primer deploy (BD vacía). En deploys posteriores se
# omite: los seeders no son idempotentes frente a ediciones del panel ni
# frente a la contraseña que el equipo del OICM ya haya cambiado.
if [ ! -f "$SEEDED_MARKER" ]; then
    info "Primer deploy detectado — sembrando datos iniciales..."
    dc exec -T app php artisan db:seed --force
    touch "$SEEDED_MARKER"
    log "Datos iniciales sembrados (marcador creado: storage-data/.seeded)"
    warn "CRÍTICO: cambia la contraseña de las cuentas de UsuarioSeeder desde /perfil AHORA"
else
    info "Siembra ya realizada anteriormente — se omite (marcador: storage-data/.seeded)"
fi

info "Regenerando cachés de Laravel..."
dc exec -T app php artisan storage:link 2>/dev/null || true
dc exec -T app php artisan config:cache
dc exec -T app php artisan route:cache
dc exec -T app php artisan view:cache
dc exec -T app php artisan cache:clear
log "Cachés generados"

echo ""
echo -e "${GREEN}========================================================"
echo "   ✓ PRODUCCIÓN desplegada"
echo "   Commit: $(git rev-parse --short HEAD)  ·  $(date '+%d/%m/%Y %H:%M:%S')"
echo ""
echo "   Sitio:  https://oicm.municipiodeoaxaca.gob.mx"
echo "   Logs:   docker compose --env-file $ENV_FILE -f $REPO_PATH/$COMPOSE_FILE logs -f app"
echo -e "========================================================${NC}"
echo ""
