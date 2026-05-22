<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Auditoría</h1>
    <p class="text-gray-500 text-sm">Registro de actividades del sistema</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="text-left px-4 py-3 font-medium">Fecha</th>
                    <th class="text-left px-4 py-3 font-medium">Usuario</th>
                    <th class="text-left px-4 py-3 font-medium">Acción</th>
                    <th class="text-left px-4 py-3 font-medium">Entidad</th>
                    <th class="text-left px-4 py-3 font-medium">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin registros de actividad.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap"><?= htmlspecialchars($log['created_at']) ?></td>
                    <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($log['user_name'] ?? 'Sistema') ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $log['action'] === 'create' ? 'bg-green-100 text-green-700' : ($log['action'] === 'update' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') ?>">
                            <?= htmlspecialchars($log['action']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <?= htmlspecialchars($log['entity_type']) ?>
                        <?php if ($log['entity_id']): ?>
                        <span class="text-gray-400">#<?= (int)$log['entity_id'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs"><?= htmlspecialchars($log['ip_address'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
