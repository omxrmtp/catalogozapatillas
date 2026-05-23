<?php

declare(strict_types=1);

define('APP_NAME', getenv('APP_NAME') ?: 'Catálogo de Zapatillas');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?: 'America/Mexico_City');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));

define('UPLOAD_MAX_SIZE', (int)(getenv('UPLOAD_MAX_SIZE') ?: 104857600));
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/avif']);

define('THUMBNAIL_WIDTH', 400);
define('MEDIUM_WIDTH', 800);
define('LARGE_WIDTH', 1200);

define('CLOUDINARY_CLOUD_NAME', getenv('CLOUDINARY_CLOUD_NAME') ?: '');
define('CLOUDINARY_API_KEY', getenv('CLOUDINARY_API_KEY') ?: '');
define('CLOUDINARY_API_SECRET', getenv('CLOUDINARY_API_SECRET') ?: '');

define('CACHE_EXPIRY', 3600);
define('RATE_LIMIT_MAX_ATTEMPTS', 5);
define('RATE_LIMIT_WINDOW', 900);
