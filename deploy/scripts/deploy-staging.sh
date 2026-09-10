#!/bin/bash
# =============================================================
# Micrositio OICM — Despliegue a STAGING
# =============================================================
# USO:
#   bash /srv/munioax/oicm/deploy/scripts/deploy-staging.sh
#
# QUÉ HACE:
#   1. Libera espacio en disco (imágenes/build cache huérfanos)
#   2. Crea el .env desde la plantilla si no existe (genera contraseñas)
#   3. Actualiza el código desde la rama 'dev'
#   4. Genera APP_KEY si falta
#   5. Reconstruye la imagen y levanta app + MySQL
#   6. Espera a MySQL, migra siempre; SIEMBRA SOLO en el primer deploy
#   7. Regenera cachés de Laravel
#
# ⚠️ A diferencia de otros portales del municipio, aquí `db:seed` NO se
# repite en cada deploy: los seeders de este proyecto (UsuarioSeeder,
# NoticiaSeeder, EnlaceSeeder, etc.) usan updateOrCreate sobre datos de
# ejemplo/catálogo con contraseña de prueba fija — correrlos de nuevo
# resetearía la contraseña del admin y pisaría lo que el equipo del OICM
# edite desde el panel. El script deja un marcador
# storage-data/.seeded tras la primera siembra exitosa y la omite después.
# Para forzar una re-siembra deliberada, borra ese marcador a mano.
#
# REQUISITOS: repo git clonado en REPO_PATH; Docker + Compose v2; openssl.
# =============================================================

set -euo pipefail

REPO_PATH="/srv/munioax/oicm"
COMPOSE_FILE="deploy/docker-compose.staging.yml"
ENV_FILE="$REPO_PATH/.env"
SEEDED_MARKER="$REPO_PATH/storage-data/.seeded"
BRANCH="dev"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
log()  { echo -e "${GREEN}  ✓ $1${NC}"; }
info() { echo -e "${BLUE}  → $1${NC}"; }
warn() { echo -e "${YELLOW}  ⚠ $1${NC}"; }
err()  { echo -e "${RED}  ✗ ERROR: $1${NC}" >&2; exit 1; }

dc() { docker compose --env-file "$ENV_FILE" -f "$COMPOSE_FILE" "$@"; }

echo ""
echo -e "${BLUE}========================================================"
echo "   Micrositio OICM — Despliegue a STAGING"
echo "   $(date '+%d/%m/%Y %H:%M:%S')"
echo -e "========================================================${NC}"
echo ""

info "Verificando requisitos..."
[ -d "$REPO_PATH" ]      || err "No existe el directorio $REPO_PATH"
[ -d "$REPO_PATH/.git" ] || err "$REPO_PATH no es un repositorio git"
command -v docker  &>/dev/null       || err "Docker no está instalado"
command -v openssl &>/dev/null       || err "openssl no está instalado"
docker compose version &>/dev/null   || err "Docker Compose v2 no está instalado"
log "Requisitos verificados"

info "Liberando espacio en disco..."
# OJO: NO usar 'docker container prune -f' ni 'docker image prune -f' sin
# filtro: son host-wide y borran contenedores/imágenes de los OTROS portales
# del servidor (mejorav4, oaxacaconecta, etc.). Solo la caché de build,
# que es regenerable y compartida — segura de purgar.
docker builder prune -f 2>/dev/null || true
log "Espacio liberado — disponible: $(df -h / | awk 'NR==2{print $4}')"

cd "$REPO_PATH"
git config --global --add safe.directory "$REPO_PATH" 2>/dev/null || true

info "Actualizando código desde GitHub (rama: $BRANCH)..."
git fetch origin
git checkout "$BRANCH"
git pull origin "$BRANCH"
log "Código actualizado — commit: $(git rev-parse --short HEAD)"

# --- Auto-setup del .env (solo si no existe; tras el pull para usar la plantilla vigente) ---
if [ ! -f "$ENV_FILE" ]; then
    info "No se encontró .env — creando desde .env.staging.example..."
    cp "$REPO_PATH/.env.staging.example" "$ENV_FILE"
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$(openssl rand -hex 20)|"      "$ENV_FILE"
    sed -i "s|^DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=$(openssl rand -hex 24)|" "$ENV_FILE"
    log ".env creado con contraseñas de BD generadas"
    warn "Revisa APP_URL en: $ENV_FILE"
fi

# --- APP_KEY si falta ---
if grep -qE '^APP_KEY=\s*(#.*)?$' "$ENV_FILE"; then
    info "Generando APP_KEY..."
    sed -i "s|^APP_KEY=.*|APP_KEY=base64:$(openssl rand -base64 32)|" "$ENV_FILE"
    log "APP_KEY generada"
fi

info "Preparando storage persistente (bind mount)..."
mkdir -p "$REPO_PATH"/storage-data/{app/public,framework/cache/data,framework/sessions,framework/views,logs}
sudo chown -R 33:33 "$REPO_PATH/storage-data"   # 33 = www-data en Debian
log "Storage listo"

info "Construyendo imagen Docker (3-5 min la primera vez)..."
dc build
log "Imagen construida"

info "Levantando servicios (app + MySQL)..."
dc up -d --remove-orphans
log "Contenedores iniciados"

info "Esperando a que MySQL esté listo..."
TIMEOUT=90; ELAPSED=0
until dc exec -T mysql mysqladmin ping -h 127.0.0.1 --silent 2>/dev/null; do
    [ $ELAPSED -ge $TIMEOUT ] && err "MySQL no respondió en ${TIMEOUT}s"
    sleep 3; ELAPSED=$((ELAPSED + 3)); echo -n "."
done
echo ""; log "MySQL listo"

# --- Config fresca antes de migrar/sembrar (evita config:cache obsoleto) ---
dc exec -T app php artisan config:clear

info "Ejecutando migraciones..."
dc exec -T app php artisan migrate --force
log "Migraciones ejecutadas"

# Siembra SOLO en el primer deploy (ver nota al inicio del script): los
# seeders de este proyecto no son seguros de repetir en una BD con datos ya
# editados desde el panel.
if [ ! -f "$SEEDED_MARKER" ]; then
    info "Primer deploy detectado — sembrando datos iniciales..."
    dc exec -T app php artisan db:seed --force
    touch "$SEEDED_MARKER"
    log "Datos iniciales sembrados (marcador creado: storage-data/.seeded)"
    warn "Cambia la contraseña de las cuentas de UsuarioSeeder desde /perfil (ver .env.staging.example)"
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

APP_PORT_VAL=$(grep '^APP_PORT=' "$ENV_FILE" | cut -d= -f2 || echo '8088')
echo ""
echo -e "${GREEN}========================================================"
echo "   ✓ STAGING desplegado"
echo "   Commit: $(git rev-parse --short HEAD)  ·  $(date '+%d/%m/%Y %H:%M:%S')"
echo ""
echo "   Sitio:  https://oicmtest.municipiodeoaxaca.gob.mx  (o http://IP_SERVIDOR:${APP_PORT_VAL})"
echo "   Admin:  https://oicmtest.municipiodeoaxaca.gob.mx/login"
echo ""
echo "   Logs:   docker compose --env-file $ENV_FILE -f $REPO_PATH/$COMPOSE_FILE logs -f app"
echo -e "========================================================${NC}"
echo ""
