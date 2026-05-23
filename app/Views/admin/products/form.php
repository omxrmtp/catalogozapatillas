<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900"><?= $product ? 'Editar' : 'Nuevo' ?> Producto</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-4xl">
    <form method="POST" enctype="multipart/form-data" id="product-form" novalidate>
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <?php if ($errors = \App\Core\Session::getFlash('errors')): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm font-medium text-red-800 mb-2">Corrige los siguientes errores:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Imagen principal</h3>
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="flex-1">
                    <label id="dropzone" class="relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-indigo-400 transition group">
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div id="dropzone-empty" class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 group-hover:text-indigo-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="mt-3 text-sm font-medium text-gray-500 group-hover:text-indigo-600 transition">Haz clic para subir imagen</span>
                            <span class="mt-1 text-xs text-gray-400">JPG, PNG o WEBP — Máx 2 MB</span>
                        </div>
                        <img id="dropzone-preview" class="hidden absolute inset-0 w-full h-full object-contain rounded-xl p-2" src="#" alt="Vista previa">
                    </label>
                </div>
                <?php if (!empty($images)): ?>
                <div class="flex-shrink-0">
                    <p class="text-sm font-medium text-gray-700 mb-2">Imagen actual</p>
                    <div class="flex gap-3 flex-wrap">
                        <?php foreach ($images as $img): ?>
                        <div class="relative group" data-image-id="<?= $img['id'] ?>">
                            <img src="<?= htmlspecialchars(cloudinary_url($img['path'], ['width' => 200, 'height' => 200, 'crop' => 'fill', 'quality' => 'auto', 'fetch_format' => 'auto'])) ?>" alt="<?= htmlspecialchars($img['alt_text'] ?? '') ?>" class="w-28 h-28 object-cover rounded-lg border border-gray-200 shadow-sm">
                            <button type="button" class="remove-image absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition flex items-center justify-center shadow" data-image-id="<?= $img['id'] ?>">
                                ✕
                            </button>
                            <?php if ($img['is_primary']): ?>
                            <span class="absolute bottom-1 left-1 px-1.5 py-0.5 bg-indigo-600 text-white text-[10px] font-semibold rounded">Principal</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Nombre *</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required maxlength="200" class="field w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" data-validate="required|min:3|max:200">
                <p class="text-xs text-gray-400 mt-1">Mínimo 3 caracteres</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="sku">SKU <span class="text-gray-400">(opcional)</span></label>
                <input type="text" name="sku" id="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" maxlength="50" class="field w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="category_id">Categoría</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="brand_id">Marca</label>
                <select name="brand_id" id="brand_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Sin marca</option>
                    <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['id'] ?>" <?= ($product['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="price">Precio (S/.) *</label>
                <input type="text" name="price" id="price" value="<?= htmlspecialchars((string)($product['price'] ?? '')) ?>" required inputmode="decimal" class="field w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" data-validate="required|numeric|min:0.01">
                <p class="text-xs text-gray-400 mt-1">Solo números y punto decimal</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="compare_price">Precio comparativo S/. <span class="text-gray-400">(opcional)</span></label>
                <input type="text" name="compare_price" id="compare_price" value="<?= htmlspecialchars((string)($product['compare_price'] ?? '')) ?>" inputmode="decimal" class="field w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" data-validate="numeric|min:0">
            </div>
            <div class="flex items-center space-x-6 pt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?= !isset($product['is_active']) || $product['is_active'] ? 'checked' : '' ?> class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Activo</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?> class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Destacado</span>
                </label>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1" for="short_description">Descripción</label>
            <textarea name="short_description" id="short_description" rows="4" maxlength="1000" class="field w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
        </div>

        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tallas y Stock</h3>
            <div id="sizes-container">
                <?php if (!empty($sizes)): ?>
                    <?php foreach ($sizes as $i => $size): ?>
                    <div class="flex items-center gap-3 mb-3 size-row">
                        <input type="text" name="sizes[<?= $i ?>][size]" value="<?= htmlspecialchars($size['size']) ?>" placeholder="Ej: 38" class="size-input w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <input type="text" name="sizes[<?= $i ?>][stock]" value="<?= (int)$size['stock'] ?>" placeholder="Stock" inputmode="numeric" class="stock-input w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <button type="button" class="remove-size px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm">Eliminar</button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="flex items-center gap-3 mb-3 size-row">
                    <input type="text" name="sizes[0][size]" placeholder="Ej: 38" class="size-input w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="sizes[0][stock]" value="0" placeholder="Stock" inputmode="numeric" class="stock-input w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <button type="button" class="remove-size px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm">Eliminar</button>
                </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add-size" class="mt-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                + Agregar talla
            </button>
            <p id="size-error" class="text-xs text-red-500 mt-2 hidden">Debes agregar al menos una talla con nombre.</p>
        </div>

        <div class="mt-8 flex items-center space-x-4">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                <?= $product ? 'Actualizar' : 'Crear' ?> producto
            </button>
            <a href="/admin/productos" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancelar</a>
        </div>
    </form>
</div>

<script>
    let sizeIndex = <?= !empty($sizes) ? count($sizes) : 1 ?>;

    function blockNonNumeric(e) {
        const allowed = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
        if (allowed.includes(e.key)) return;
        if (e.key === '.' && !e.target.value.includes('.')) return;
        if (e.key === '.' && e.target.value.includes('.')) { e.preventDefault(); return; }
        if (!/^[0-9]$/.test(e.key)) { e.preventDefault(); }
    }

    function blockNonNumericInt(e) {
        const allowed = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
        if (allowed.includes(e.key)) return;
        if (!/^[0-9]$/.test(e.key)) { e.preventDefault(); }
    }

    function validateField(el) {
        const rules = (el.dataset.validate || '').split('|');
        const val = el.value.trim();
        let error = '';

        for (const rule of rules) {
            if (rule === 'required' && !val) { error = 'Este campo es obligatorio'; break; }
            if (rule.startsWith('min:') && val) {
                const min = parseInt(rule.split(':')[1]);
                if (rule.includes('numeric') || el.id === 'price' || el.id === 'compare_price') {
                    if (parseFloat(val) < min) { error = `El valor mínimo es ${min}`; break; }
                } else {
                    if (val.length < min) { error = `Mínimo ${min} caracteres`; break; }
                }
            }
            if (rule.startsWith('max:') && val && val.length > parseInt(rule.split(':')[1])) {
                error = `Máximo ${rule.split(':')[1]} caracteres`; break;
            }
            if (rule === 'numeric' && val && isNaN(parseFloat(val))) { error = 'Debe ser un número'; break; }
        }

        const parent = el.closest('div');
        const existing = parent?.querySelector('.field-error');
        if (existing) existing.remove();

        if (error) {
            el.classList.add('border-red-500', 'bg-red-50');
            el.classList.remove('border-gray-300');
            if (parent) {
                const errEl = document.createElement('p');
                errEl.className = 'field-error text-xs text-red-500 mt-1';
                errEl.textContent = error;
                parent.appendChild(errEl);
            }
            return false;
        }
        el.classList.remove('border-red-500', 'bg-red-50');
        el.classList.add('border-gray-300');
        return true;
    }

    document.querySelectorAll('.field[data-validate]').forEach(el => {
        el.addEventListener('blur', () => validateField(el));
        el.addEventListener('input', () => {
            if (el.classList.contains('border-red-500')) validateField(el);
        });
    });

    document.getElementById('price')?.addEventListener('keydown', blockNonNumeric);
    document.getElementById('compare_price')?.addEventListener('keydown', blockNonNumeric);

    document.getElementById('image')?.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('dropzone-empty').classList.add('hidden');
            const preview = document.getElementById('dropzone-preview');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    document.querySelectorAll('.stock-input').forEach(el => {
        el.addEventListener('keydown', blockNonNumericInt);
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('stock-input')) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        }
    });

    document.getElementById('add-size')?.addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 mb-3 size-row';
        row.innerHTML = `
            <input type="text" name="sizes[${sizeIndex}][size]" placeholder="Ej: 38" class="size-input w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <input type="text" name="sizes[${sizeIndex}][stock]" value="0" placeholder="Stock" inputmode="numeric" class="stock-input w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <button type="button" class="remove-size px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm">Eliminar</button>
        `;
        row.querySelector('.stock-input').addEventListener('keydown', blockNonNumericInt);
        container.appendChild(row);
        sizeIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size')) {
            e.target.closest('.size-row').remove();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-image')) {
            const imgId = e.target.dataset.imageId;
            const container = e.target.closest('[data-image-id]');
            showConfirm('¿Estás seguro de eliminar esta imagen? Esta acción no se puede deshacer.').then(confirmed => {
                if (!confirmed) return;
                fetch('/admin/productos/imagen/eliminar/' + imgId, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'No se pudo eliminar la imagen.'));
                        }
                    })
                    .catch(() => alert('Error de conexión.'));
            });
        }
    });

    document.getElementById('product-form')?.addEventListener('submit', function(e) {
        let valid = true;

        document.querySelectorAll('.field[data-validate]').forEach(el => {
            if (!validateField(el)) valid = false;
        });

        const hasSize = Array.from(document.querySelectorAll('.size-input')).some(el => el.value.trim() !== '');
        const sizeError = document.getElementById('size-error');
        if (!hasSize) {
            sizeError.classList.remove('hidden');
            valid = false;
        } else {
            sizeError.classList.add('hidden');
        }

        if (!valid) {
            e.preventDefault();
            const firstError = document.querySelector('.border-red-500');
            firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError?.focus();
        }
    });
</script>
