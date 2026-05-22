<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <div class="text-4xl mb-2">👟</div>
            <h1 class="text-2xl font-bold text-gray-900">Admin CRM</h1>
            <p class="text-gray-500 text-sm mt-1">Inicia sesión para gestionar el catálogo</p>
        </div>

        <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
            <?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/admin/login">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" id="email" name="email" required autocomplete="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Iniciar sesión
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-gray-500 hover:text-indigo-600 transition">← Volver al inicio</a>
        </div>
    </div>
</div>
