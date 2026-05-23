<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($metaTitle ?? APP_NAME) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? '') ?>">
    <?php if (isset($ogImage)): ?>
    <meta property="og:image" content="<?= htmlspecialchars(cloudinary_url($ogImage, ['width' => 1200, 'height' => 630, 'crop' => 'fill', 'quality' => 'auto', 'fetch_format' => 'auto'])) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= htmlspecialchars($metaTitle ?? APP_NAME) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription ?? '') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="<?= APP_URL . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-2xl font-extrabold text-indigo-600">👟</span>
                    <span class="text-xl font-bold text-gray-900">CatálogoZapatillas</span>
                </a>
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-indigo-600 font-medium transition">Inicio</a>
                    <a href="/catalogo" class="text-gray-700 hover:text-indigo-600 font-medium transition">Catálogo</a>
                    <?php if ($isLoggedIn): ?>
                    <a href="/admin/dashboard" class="text-gray-700 hover:text-indigo-600 font-medium transition">Admin</a>
                    <?php endif; ?>
                </nav>
                <button id="menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100" aria-label="Menú">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <a href="/" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 font-medium">Inicio</a>
            <a href="/catalogo" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 font-medium">Catálogo</a>
            <?php if ($isLoggedIn): ?>
            <a href="/admin/dashboard" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 font-medium">Admin</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?>
            </div>
        </div>
        <?php endif; ?>
        <?= $content ?>
    </main>

    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">👟 CatálogoZapatillas</h3>
                    <p class="text-sm">Tu catálogo de zapatillas favorito. Explora las mejores marcas y modelos.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Enlaces</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="hover:text-white transition">Inicio</a></li>
                        <li><a href="/catalogo" class="hover:text-white transition">Catálogo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contacto</h4>
                    <p class="text-sm">contacto@catalogozapatillas.com</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm">
                <p>&copy; <?= date('Y') ?> CatálogoZapatillas. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
    </script>
</body>
</html>
