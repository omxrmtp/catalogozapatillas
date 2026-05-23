<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Productos</h1>
        <p class="text-gray-500 text-sm"><?= $total ?> producto(s)</p>
    </div>
    <a href="/admin/productos/crear" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">+ Nuevo producto</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 border-b border-gray-200">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Buscar por nombre o SKU..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition text-sm">Filtrar</button>
        </form>
    </div>

    <?php if (empty($products)): ?>
    <div class="text-center py-12">
        <p class="text-gray-500">No hay productos.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="text-left px-4 py-3 font-medium w-12">Imagen</th>
                    <th class="text-left px-4 py-3 font-medium">Producto</th>
                    <th class="text-left px-4 py-3 font-medium hidden md:table-cell">Categoría</th>
                    <th class="text-left px-4 py-3 font-medium hidden md:table-cell">Marca</th>
                    <th class="text-right px-4 py-3 font-medium">Precio</th>
                    <th class="text-right px-4 py-3 font-medium">Stock</th>
                    <th class="text-center px-4 py-3 font-medium">Estado</th>
                    <th class="text-right px-4 py-3 font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($products as $p): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <?php if (!empty($p['primary_image'])): ?>
                        <img src="<?= htmlspecialchars(cloudinary_url($p['primary_image'], ['width' => 80, 'height' => 80, 'crop' => 'fill', 'quality' => 'auto', 'fetch_format' => 'auto'])) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-10 h-10 rounded object-cover">
                        <?php else: ?>
                        <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($p['name']) ?></p>
                        <p class="text-xs text-gray-500">SKU: <?= htmlspecialchars($p['sku'] ?? '—') ?></p>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-gray-600"><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                    <td class="px-4 py-3 hidden md:table-cell text-gray-600"><?= htmlspecialchars($p['brand_name'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-right font-medium">S/. <?= number_format((float)$p['price'], 2) ?></td>
                    <td class="px-4 py-3 text-right">
                        <span class="<?= (int)$p['stock'] <= 5 ? 'text-orange-600 font-semibold' : 'text-gray-600' ?>"><?= (int)$p['stock'] ?></span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button class="toggle-product px-2 py-1 text-xs font-medium rounded-full <?= $p['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>" data-id="<?= $p['id'] ?>">
                            <?= $p['is_active'] ? 'Activo' : 'Inactivo' ?>
                        </button>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="/admin/productos/editar/<?= $p['id'] ?>" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Editar</a>
                        <button type="button" class="delete-product text-red-600 hover:text-red-800 font-medium text-sm" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div class="flex justify-center p-4 border-t border-gray-200">
        <div class="flex space-x-1">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['category_id']) ? '&category_id=' . $filters['category_id'] : '' ?>" class="px-3 py-1 text-sm rounded-md <?= $i === $page ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.toggle-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch('/admin/productos/toggle/' + id, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.textContent = data.is_active ? 'Activo' : 'Inactivo';
                        this.className = 'toggle-product px-2 py-1 text-xs font-medium rounded-full ' +
                            (data.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500');
                    }
                });
        });
    });

    document.querySelectorAll('.delete-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const name = this.dataset.name;
            const id = this.dataset.id;
            const row = this.closest('tr');
            showConfirm('¿Estás seguro de eliminar el producto <strong>"' + name + '"</strong>? Esta acción no se puede deshacer.').then(confirmed => {
                if (!confirmed) return;
                fetch('/admin/productos/eliminar/' + id, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                        } else {
                            alert('Error: ' + (data.error || 'No se pudo eliminar el producto.'));
                        }
                    })
                    .catch(() => alert('Error de conexión. Intente nuevamente.'));
            });
        });
    });
</script>
