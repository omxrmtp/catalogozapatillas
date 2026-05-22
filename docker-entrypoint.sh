#!/bin/bash
set -e

echo "=== Esperando a que MySQL estÃ© listo... ==="
until php -r "
    try {
        new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};dbname=${DB_NAME:-catalogozapatillas}', '${DB_USER:-catalog_user}', '${DB_PASS:-catalog_pass}');
        echo 'connected';
    } catch(PDOException \$e) {
        sleep(1);
    }
" 2>/dev/null | grep -q 'connected'; do
    sleep 1
done
echo "âœ“ MySQL estÃ¡ listo"

echo "=== Ejecutando migraciones ==="
php /var/www/html/migrate.php
echo "âœ“ Migraciones completadas"

echo "=== Iniciando Apache ==="
exec "$@"
