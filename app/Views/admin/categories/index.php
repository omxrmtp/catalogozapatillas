<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Categorías</h1>
        <p class="text-gray-500 text-sm"><?= count($categories) ?> categoría(s)</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Nueva categoría</h2>
        <form method="POST" action="/admin/categorias/crear">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <label class="flex items-center mb-4">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Activo</span>
            </label>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">Crear</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Categorías existentes</h2>
        <?php if (empty($categories)): ?>
        <p class="text-sm text-gray-500">No hay categorías.</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($categories as $cat): ?>
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-900"><?= htmlspecialchars($cat['name']) ?></p>
                    <p class="text-xs text-gray-500">Orden: <?= (int)$cat['sort_order'] ?> | <?= $cat['is_active'] ? 'Activo' : 'Inactivo' ?></p>
                </div>
                <form method="POST" action="/admin/categorias/editar/<?= $cat['id'] ?>" class="flex items-center space-x-2">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" class="px-2 py-1 border border-gray-300 rounded text-sm w-32">
                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Guardar</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
