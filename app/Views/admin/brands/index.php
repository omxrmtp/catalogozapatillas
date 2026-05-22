<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Marcas</h1>
        <p class="text-gray-500 text-sm"><?= count($brands) ?> marca(s)</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Nueva marca</h2>
        <form method="POST" action="/admin/marcas/crear">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <label class="flex items-center mb-4">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Activo</span>
            </label>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">Crear</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Marcas existentes</h2>
        <?php if (empty($brands)): ?>
        <p class="text-sm text-gray-500">No hay marcas.</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($brands as $brand): ?>
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-900"><?= htmlspecialchars($brand['name']) ?></p>
                    <p class="text-xs text-gray-500"><?= $brand['is_active'] ? 'Activo' : 'Inactivo' ?></p>
                </div>
                <form method="POST" action="/admin/marcas/editar/<?= $brand['id'] ?>" class="flex items-center space-x-2">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($brand['name']) ?>" class="px-2 py-1 border border-gray-300 rounded text-sm w-32">
                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Guardar</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
