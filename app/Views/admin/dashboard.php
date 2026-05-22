<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-500">Resumen del catálogo</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Productos activos</p>
        <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int)$kpis['total_active_products'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Stock bajo</p>
        <p class="text-3xl font-bold text-orange-600 mt-1"><?= (int)$kpis['low_stock_count'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Categorías activas</p>
        <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int)$kpis['active_categories'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Stock total</p>
        <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int)$kpis['total_stock'] ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Productos por categoría</h2>
        <canvas id="categoryChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Top productos más vistos</h2>
        <div class="space-y-3">
            <?php foreach ($topProducts as $i => $p): ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-400 w-6">#<?= $i + 1 ?></span>
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($p['name']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($p['brand_name'] ?? '') ?></p>
                    </div>
                </div>
                <span class="text-sm font-semibold text-gray-700"><?= (int)$p['views_count'] ?> vistas</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock bajo</h2>
        <?php if (empty($lowStock)): ?>
        <p class="text-sm text-gray-500">No hay productos con stock bajo.</p>
        <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($lowStock as $p): ?>
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($p['name']) ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($p['brand_name'] ?? '') ?></p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700"><?= (int)$p['stock'] ?> uds</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Actividad reciente</h2>
        <?php if (empty($recentLogs)): ?>
        <p class="text-sm text-gray-500">Sin actividad reciente.</p>
        <?php else: ?>
        <div class="space-y-2 max-h-80 overflow-y-auto">
            <?php foreach ($recentLogs as $log): ?>
            <div class="flex items-start space-x-3 py-2 border-b border-gray-100 last:border-0">
                <div class="w-2 h-2 mt-2 rounded-full flex-shrink-0 <?= $log['action'] === 'create' ? 'bg-green-500' : ($log['action'] === 'update' ? 'bg-blue-500' : 'bg-gray-500') ?>"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">
                        <span class="font-medium"><?= htmlspecialchars($log['user_name'] ?? 'Sistema') ?></span>
                        <?= htmlspecialchars($log['action']) ?>
                        <span class="text-gray-500"><?= htmlspecialchars($log['entity_type']) ?></span>
                    </p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($log['created_at']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const ctx = document.getElementById('categoryChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(fn($c) => $c['name'], $productsByCategory)) ?>,
                datasets: [{
                    label: 'Productos',
                    data: <?= json_encode(array_map(fn($c) => (int)$c['product_count'], $productsByCategory)) ?>,
                    backgroundColor: ['#4f46e5', '#7c3aed', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
