<?php

declare(strict_types=1);

$dbUrl = getenv('DATABASE_URL');

if ($dbUrl) {
    $parts = parse_url($dbUrl);
    $scheme = $parts['scheme'] ?? 'pgsql';
    $driver = match ($scheme) {
        'postgres', 'postgresql' => 'pgsql',
        'mysql', 'mariadb'       => 'mysql',
        default                  => $scheme,
    };
    $host = $parts['host'] ?? 'localhost';
    $port = (string)($parts['port'] ?? ($driver === 'pgsql' ? '5432' : '3306'));
    $dbname = ltrim($parts['path'] ?? '', '/') ?: 'neondb';
    $user = $parts['user'] ?? 'root';
    $pass = $parts['pass'] ?? '';

    parse_str($parts['query'] ?? '', $query);
    $sslmode = $query['sslmode'] ?? '';

    putenv("DB_DRIVER=$driver");
    putenv("DB_HOST=$host");
    putenv("DB_PORT=$port");
    putenv("DB_NAME=$dbname");
    putenv("DB_USER=$user");
    putenv("DB_PASS=$pass");
    putenv("DB_SSLMODE=$sslmode");
}

define('DB_DRIVER', getenv('DB_DRIVER') ?: 'mysql');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: (DB_DRIVER === 'pgsql' ? '5432' : '3306'));
define('DB_NAME', getenv('DB_NAME') ?: 'catalogozapatillas');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: (DB_DRIVER === 'pgsql' ? 'utf8' : 'utf8mb4'));
define('DB_SSLMODE', getenv('DB_SSLMODE') ?: '');
