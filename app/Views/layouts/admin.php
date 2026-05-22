<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($metaTitle ?? 'Admin') ?> | CatálogoZapatillas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover { background-color: #eef2ff; color: #4f46e5; }
        .sidebar-link.active { background-color: #eef2ff; color: #4f46e5; border-right: 3px solid #4f46e5; }
        .modal-overlay { opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
        .modal-overlay.open { opacity: 1; pointer-events: auto; }
        .modal-card { transform: scale(0.85) translateY(20px); transition: transform 0.25s ease; }
        .modal-overlay.open .modal-card { transform: scale(1) translateY(0); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="bg-white w-64 border-r border-gray-200 flex-shrink-0 hidden md:block overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <a href="/admin/dashboard" class="flex items-center space-x-2">
                    <span class="text-xl font-extrabold text-indigo-600">👟</span>
                    <span class="font-bold text-gray-900">Admin CRM</span>
                </a>
            </div>
            <nav class="p-4 space-y-1">
                <a href="/admin/dashboard" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-700 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="/admin/productos" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-700 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Productos
                </a>
                <a href="/admin/categorias" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-700 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Categorías
                </a>
                <a href="/admin/marcas" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-700 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Marcas
                </a>
                <a href="/admin/usuarios" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-700 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    Usuarios
                </a>
                <a href="/admin/auditoria" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-700 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Auditoría
                </a>
                <hr class="my-4">
                <a href="/" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-500 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver al sitio
                </a>
                <a href="/admin/logout" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-red-600 font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar sesión
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600"><?= htmlspecialchars(\App\Core\Session::get('user_name', '')) ?></span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= \App\Core\Session::isSuperAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                        <?= \App\Core\Session::getUserRole() === 'super_admin' ? 'Super Admin' : 'Editor' ?>
                    </span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <?php if (\App\Core\Session::hasFlash('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?>
                </div>
                <?php endif; ?>
                <?php if (\App\Core\Session::hasFlash('error')): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?>
                </div>
                <?php endif; ?>
                <?= $content ?>
            </main>
        </div>
    </div>

    <div id="confirmModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="modal-card bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-100">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirmar eliminación</h3>
            <p class="text-sm text-gray-600 text-center mb-6" id="confirmMessage">¿Estás seguro?</p>
            <div class="flex gap-3">
                <button id="confirmCancel" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancelar</button>
                <button id="confirmDelete" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('aside');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-0');
                sidebar.classList.toggle('z-50');
            }
        });

        let confirmResolve = null;

        function showConfirm(message) {
            return new Promise(resolve => {
                confirmResolve = resolve;
                document.getElementById('confirmMessage').innerHTML = message;
                document.getElementById('confirmModal').classList.add('open');
            });
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.remove('open');
            if (confirmResolve) confirmResolve(true);
        });

        document.getElementById('confirmCancel').addEventListener('click', closeConfirm);
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) closeConfirm();
        });

        function closeConfirm() {
            document.getElementById('confirmModal').classList.remove('open');
            if (confirmResolve) confirmResolve(false);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('confirmModal').classList.contains('open')) {
                closeConfirm();
            }
        });
    </script>
</body>
</html>
