<?php

declare(strict_types=1);

/**
 * One-time setup script for production.
 * Run ONCE after deploying: php migrate.php
 * Or hit it in the browser and delete it after.
 */

require __DIR__ . '/bootstrap.php';

use App\Core\Database;

$output = function (string $msg): void {
    echo $msg . "\n";
    if (PHP_SAPI !== 'cli') {
        echo "<br>";
    }
};

$output("=== Migrando base de datos: " . DB_NAME . " ===");

try {
    $db = Database::getInstance()->getConnection();
    $output("✓ Conexión exitosa");
} catch (\Throwable $e) {
    $output("✗ Error de conexión: " . $e->getMessage());
    exit(1);
}

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

$executed = 0;

foreach ($files as $file) {
    $basename = basename($file);
    $sql = file_get_contents($file);

    if ($sql === false || trim($sql) === '') {
        $output("  ↻ {$basename}: vacío, saltando");
        continue;
    }

    try {
        // Split by semi-colons for multi-statement execution
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn(string $s) => $s !== ''
        );

        foreach ($statements as $stmt) {
            $db->exec($stmt);
        }

        $output("  ✓ {$basename}");
        $executed++;
    } catch (\Throwable $e) {
        if (str_contains($e->getMessage(), 'Duplicate')) {
            $output("  ~ {$basename}: datos ya existen, saltando");
        } else {
            $output("  ✗ {$basename}: " . $e->getMessage());
        }
    }
}

$output("=== Migración completada. {$executed} archivos ejecutados. ===");
$output("Elimina este archivo (migrate.php) después de usarlo por seguridad.");
