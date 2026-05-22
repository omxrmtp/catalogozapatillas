<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
        <p class="text-gray-500 text-sm"><?= count($users) ?> usuario(s)</p>
    </div>
    <a href="/admin/usuarios/crear" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">+ Nuevo usuario</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <th class="text-left px-4 py-3 font-medium">Nombre</th>
                <th class="text-left px-4 py-3 font-medium hidden md:table-cell">Correo</th>
                <th class="text-left px-4 py-3 font-medium">Rol</th>
                <th class="text-center px-4 py-3 font-medium">Estado</th>
                <th class="text-right px-4 py-3 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($users as $u): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($u['name']) ?></td>
                <td class="px-4 py-3 hidden md:table-cell text-gray-600"><?= htmlspecialchars($u['email']) ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $u['role'] === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                        <?= $u['role'] === 'super_admin' ? 'Super Admin' : 'Editor' ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $u['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= $u['is_active'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="/admin/usuarios/editar/<?= $u['id'] ?>" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
