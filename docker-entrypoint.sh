#!/bin/bash
set -e

# ── Configurar puerto de Apache (Render usa PORT=10000) ──
if [ -n "$PORT" ] && [ "$PORT" -ne 80 ]; then
    echo "=== Configurando Apache en puerto $PORT ==="
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80>/:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# ── Esperar a que la base de datos esté lista (máx 60s) ──
if [ -n "${DB_HOST}" ]; then
    echo "=== Esperando a ${DB_DRIVER}... ==="
    MAX_ATTEMPTS=30
    attempt=0
    until php -r "
        try {
            new PDO('${DB_DRIVER}:host=${DB_HOST};port=${DB_PORT};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
            echo 'connected';
        } catch(PDOException \$e) {
            exit(1);
        }
    " 2>/dev/null | grep -q 'connected'; do
        attempt=$((attempt+1))
        if [ $attempt -ge $MAX_ATTEMPTS ]; then
            echo "⚠  Base de datos no disponible tras ${MAX_ATTEMPTS}s, continuando..."
            break
        fi
        sleep 1
    done
    if [ $attempt -lt $MAX_ATTEMPTS ]; then
        echo "✓ ${DB_DRIVER} listo"
    fi
fi

# ── Migraciones ──
echo "=== Ejecutando migraciones ==="
php /var/www/html/migrate.php
echo "✓ Migraciones completadas"

echo "=== Iniciando Apache ==="
exec "$@"
