#!/bin/bash
set -e

# â”€â”€ Configurar puerto de Apache (Render usa PORT=10000) â”€â”€
if [ -n "$PORT" ] && [ "$PORT" -ne 80 ]; then
    echo "=== Configurando Apache en puerto $PORT ==="
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80>/:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# â”€â”€ Esperar a que la base de datos estÃ© lista â”€â”€
if [ "${DB_DRIVER}" = "pgsql" ]; then
    echo "=== Esperando a PostgreSQL... ==="
    until php -r "
        try {
            new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
            echo 'connected';
        } catch(PDOException \$e) {
            sleep(1);
        }
    " 2>/dev/null | grep -q 'connected'; do
        sleep 1
    done
    echo "âœ“ PostgreSQL listo"
elif [ "${DB_DRIVER}" = "mysql" ]; then
    echo "=== Esperando a MySQL... ==="
    until php -r "
        try {
            new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');
            echo 'connected';
        } catch(PDOException \$e) {
            sleep(1);
        }
    " 2>/dev/null | grep -q 'connected'; do
        sleep 1
    done
    echo "âœ“ MySQL listo"
fi

# â”€â”€ Migraciones â”€â”€
echo "=== Ejecutando migraciones ==="
php /var/www/html/migrate.php
echo "âœ“ Migraciones completadas"

echo "=== Iniciando Apache ==="
exec "$@"
