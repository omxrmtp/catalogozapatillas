<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="/" class="hover:text-indigo-600">Inicio</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium">Catálogo</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <aside id="filter-sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-white shadow-xl border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 lg:shadow-none lg:border-0 lg:w-64 lg:flex-shrink-0 lg:z-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 lg:hidden">
                <h3 class="font-bold text-gray-900">Filtros</h3>
                <button id="filter-close" class="p-2 rounded-lg hover:bg-gray-100 transition" aria-label="Cerrar filtros">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto h-full lg:h-auto">
                <h3 class="font-bold text-gray-900 mb-4 hidden lg:block">Filtros</h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" id="search-input" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Buscar zapatillas..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <?php if (!empty($categories)): ?>
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Categorías</h4>
                    <div class="space-y-2">
                        <a href="/catalogo" class="block text-sm <?= empty($filters['category']) ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-indigo-600' ?>">Todas</a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="/catalogo?category=<?= htmlspecialchars($cat['slug']) ?>" class="block text-sm <?= ($filters['category'] ?? '') === $cat['slug'] ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-indigo-600' ?>">
                            <?= htmlspecialchars($cat['name']) ?> (<?= (int)$cat['product_count'] ?>)
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($brands)): ?>
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Marcas</h4>
                    <div class="space-y-2">
                        <a href="/catalogo" class="block text-sm <?= empty($filters['brand']) ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-indigo-600' ?>">Todas</a>
                        <?php foreach ($brands as $brand): ?>
                        <a href="/catalogo?brand=<?= htmlspecialchars($brand['slug']) ?>" class="block text-sm <?= ($filters['brand'] ?? '') === $brand['slug'] ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-indigo-600' ?>">
                            <?= htmlspecialchars($brand['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </aside>

        <div id="filter-overlay" class="fixed inset-0 z-30 bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <button id="filter-toggle" class="lg:hidden inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtros
                    </button>
                    <p class="text-sm text-gray-600"><?= $total ?> producto(s)</p>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600 hidden sm:inline">Ordenar:</label>
                    <select id="sort-select" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="p.created_at DESC" <?= ($filters['sort'] ?? '') === 'p.created_at DESC' ? 'selected' : '' ?>>Más recientes</option>
                        <option value="p.price ASC" <?= ($filters['sort'] ?? '') === 'p.price ASC' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="p.price DESC" <?= ($filters['sort'] ?? '') === 'p.price DESC' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                        <option value="p.name ASC" <?= ($filters['sort'] ?? '') === 'p.name ASC' ? 'selected' : '' ?>>Nombre A-Z</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
            <div class="text-center py-16">
                <div class="text-6xl mb-4">👟</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No hay productos</h3>
                <p class="text-gray-500">No se encontraron productos con los filtros seleccionados.</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                <?php foreach ($products as $product): ?>
                <a href="/producto/<?= htmlspecialchars($product['slug']) ?>" class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                        <?php if ($product['primary_image']): ?>
                        <img src="<?= htmlspecialchars($product['primary_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                        <?php else: ?>
                        <div class="text-6xl">👟</div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <?php if ($product['brand_name']): ?>
                        <p class="text-xs font-medium text-indigo-600 uppercase tracking-wider"><?= htmlspecialchars($product['brand_name']) ?></p>
                        <?php endif; ?>
                        <h3 class="font-semibold text-gray-900 mt-1"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="text-lg font-bold text-gray-900 mt-2">S/. <?= number_format((float)$product['price'], 2) ?></p>
                        <?php if ($product['compare_price'] && (float)$product['compare_price'] > (float)$product['price']): ?>
                        <p class="text-sm text-gray-500 line-through">S/. <?= number_format((float)$product['compare_price'], 2) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($product['sizes'])): ?>
                        <p class="text-sm font-semibold text-gray-700 mt-2">Tallas: <span class="text-indigo-600 font-bold"><?= htmlspecialchars($product['sizes']) ?></span></p>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if ($pages > 1): ?>
            <div class="flex justify-center mt-8 space-x-2">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($filters['category']) ? '&category=' . urlencode($filters['category']) : '' ?><?= !empty($filters['brand']) ? '&brand=' . urlencode($filters['brand']) : '' ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?= $i === $page ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const filterToggle = document.getElementById('filter-toggle');
    const filterClose = document.getElementById('filter-close');
    const filterSidebar = document.getElementById('filter-sidebar');
    const filterOverlay = document.getElementById('filter-overlay');

    function openFilters() {
        filterSidebar.classList.remove('-translate-x-full');
        filterOverlay.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
    }

    function closeFilters() {
        filterSidebar.classList.add('-translate-x-full');
        filterOverlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    }

    filterToggle?.addEventListener('click', openFilters);
    filterClose?.addEventListener('click', closeFilters);
    filterOverlay?.addEventListener('click', closeFilters);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !filterSidebar.classList.contains('-translate-x-full')) {
            closeFilters();
        }
    });

    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    let searchTimeout;

    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const params = new URLSearchParams(window.location.search);
            if (this.value) {
                params.set('search', this.value);
            } else {
                params.delete('search');
            }
            params.set('page', '1');
            window.location.search = params.toString();
        }, 500);
    });

    sortSelect?.addEventListener('change', function() {
        const params = new URLSearchParams(window.location.search);
        params.set('sort', this.value);
        params.set('page', '1');
        window.location.search = params.toString();
    });
</script>
