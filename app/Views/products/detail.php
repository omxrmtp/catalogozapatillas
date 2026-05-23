<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-4 mb-6">
        <button onclick="history.back()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition shadow-sm" aria-label="Volver">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </button>
    </div>

    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="/" class="hover:text-indigo-600">Inicio</a>
        <span class="mx-2">/</span>
        <a href="/catalogo" class="hover:text-indigo-600">Catálogo</a>
        <?php if ($product['category_name']): ?>
        <span class="mx-2">/</span>
        <a href="/catalogo?category=<?= htmlspecialchars($product['category_slug']) ?>" class="hover:text-indigo-600"><?= htmlspecialchars($product['category_name']) ?></a>
        <?php endif; ?>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium"><?= htmlspecialchars($product['name']) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
        <div>
            <div id="image-zoom-container" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden mb-4 relative cursor-crosshair">
                <?php if (!empty($images)): ?>
                <picture id="main-picture">
                    <?php if ($images[0]['path_avif']): ?>
                    <source srcset="<?= htmlspecialchars(cloudinary_url($images[0]['path_avif'], ['width' => 800, 'quality' => 'auto'])) ?>" type="image/avif">
                    <?php endif; ?>
                    <?php if ($images[0]['path_webp']): ?>
                    <source srcset="<?= htmlspecialchars(cloudinary_url($images[0]['path_webp'], ['width' => 800, 'quality' => 'auto'])) ?>" type="image/webp">
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars(cloudinary_url($images[0]['path'], ['width' => 800, 'quality' => 'auto', 'fetch_format' => 'auto'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover" id="main-image">
                </picture>
                <div id="zoom-lens" class="hidden absolute top-0 left-0 w-32 h-32 bg-white/20 border-2 border-indigo-500 rounded-full pointer-events-none"></div>
                <div id="zoom-result" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 w-[90vw] h-[90vh] max-w-4xl max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden p-2">
                    <button id="zoom-close-btn" class="absolute top-4 right-4 z-10 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center shadow hover:bg-gray-100 transition" aria-label="Cerrar zoom">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <img src="" alt="" class="w-full h-full object-contain" id="zoom-result-img">
                </div>
                <div id="zoom-overlay" class="hidden fixed inset-0 z-40 bg-black/60" onclick="closeZoom()"></div>
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-8xl">👟</div>
                <?php endif; ?>
            </div>
            <?php if (count($images) > 1): ?>
            <div class="grid grid-cols-4 gap-2">
                <?php foreach ($images as $img): ?>
                <button class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-transparent hover:border-indigo-500 transition thumb-btn" data-src="<?= htmlspecialchars(cloudinary_url($img['path'], ['width' => 800, 'quality' => 'auto', 'fetch_format' => 'auto'])) ?>">
                    <img src="<?= htmlspecialchars(cloudinary_url($img['path'], ['width' => 200, 'height' => 200, 'crop' => 'fill', 'quality' => 'auto', 'fetch_format' => 'auto'])) ?>" alt="" class="w-full h-full object-cover" loading="lazy">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <?php if ($product['brand_name']): ?>
            <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider"><?= htmlspecialchars($product['brand_name']) ?></p>
            <?php endif; ?>
            <h1 class="text-3xl font-bold text-gray-900 mt-1"><?= htmlspecialchars($product['name']) ?></h1>

            <div class="mt-6">
                <p class="text-3xl font-bold text-gray-900">S/. <?= number_format((float)$product['price'], 2) ?></p>
                <?php if ($product['compare_price'] && (float)$product['compare_price'] > (float)$product['price']): ?>
                <p class="text-lg text-gray-500 line-through mt-1">S/. <?= number_format((float)$product['compare_price'], 2) ?></p>
                <span class="inline-block mt-1 px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">-<?= round((1 - (float)$product['price'] / (float)$product['compare_price']) * 100) ?>%</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($sizes)): ?>
            <div class="mt-8">
                <h3 class="text-base font-bold text-gray-900 mb-4">Tallas disponibles</h3>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($sizes as $size): ?>
                    <button class="size-btn px-5 py-3 border-2 rounded-xl text-base font-bold transition shadow-sm <?= (int)$size['stock'] > 0 ? 'border-indigo-300 bg-indigo-50 text-gray-900 hover:bg-indigo-100 hover:border-indigo-500' : 'border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed' ?>" data-size="<?= htmlspecialchars($size['size']) ?>" <?= (int)$size['stock'] === 0 ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($size['size']) ?>
                        <?php if ((int)$size['stock'] <= 5 && (int)$size['stock'] > 0): ?>
                        <span class="block text-xs font-medium text-orange-500">Últimas</span>
                        <?php endif; ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($product['short_description']): ?>
            <p class="mt-6 text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($product['short_description'])) ?></p>
            <?php endif; ?>

            <?php if ($product['description']): ?>
            <div class="mt-8">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Descripción</h3>
                <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
            <?php endif; ?>

            <?php if ($product['sku']): ?>
            <p class="mt-6 text-sm text-gray-400">SKU: <?= htmlspecialchars($product['sku']) ?></p>
            <?php endif; ?>

            <?php
            $waMsg = 'Hola, me interesa este producto:' . "\n";
            $waMsg .= '📍 ' . $product['name'] . "\n";
            if ($product['brand_name']) $waMsg .= '🏷️ Marca: ' . $product['brand_name'] . "\n";
            if ($product['category_name']) $waMsg .= '📂 Categoría: ' . $product['category_name'] . "\n";
            $waMsg .= '💰 Precio: S/. ' . number_format((float)$product['price'], 2) . "\n";
            if ($product['sku']) $waMsg .= '🔖 SKU: ' . $product['sku'] . "\n";
            if (!empty($sizes)) {
                $tmp = [];
                foreach ($sizes as $s) $tmp[] = $s['size'];
                $waMsg .= '👟 Tallas disponibles: ' . implode(', ', $tmp);
            }
            ?>
            <a href="https://wa.me/51950695005?text=<?= urlencode($waMsg) ?>" target="_blank" rel="noopener" class="mt-6 inline-flex items-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Consultar por WhatsApp
            </a>
        </div>
    </div>
</div>

<script>
    const zoomContainer = document.getElementById('image-zoom-container');
    const mainImage = document.getElementById('main-image');
    const zoomLens = document.getElementById('zoom-lens');
    const zoomResult = document.getElementById('zoom-result');
    const zoomResultImg = document.getElementById('zoom-result-img');
    const zoomOverlay = document.getElementById('zoom-overlay');

    function openZoom(src) {
        zoomResultImg.src = src;
        zoomResult.classList.remove('hidden');
        zoomOverlay.classList.remove('hidden');
        document.documentElement.classList.add('overflow-hidden');
    }

    function closeZoom() {
        zoomResult.classList.add('hidden');
        zoomOverlay.classList.add('hidden');
        document.documentElement.classList.remove('overflow-hidden');
    }

    if (zoomContainer && mainImage) {
        zoomContainer.addEventListener('click', function(e) {
            if (mainImage.src) openZoom(mainImage.src);
        });

        zoomContainer.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;
            mainImage.style.transformOrigin = xPercent + '% ' + yPercent + '%';
            mainImage.style.transform = 'scale(2)';
            mainImage.style.transition = 'none';
        });

        zoomContainer.addEventListener('mouseleave', function() {
            mainImage.style.transform = 'scale(1)';
            mainImage.style.transformOrigin = 'center center';
            mainImage.style.transition = 'transform 0.3s ease';
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !zoomResult.classList.contains('hidden')) closeZoom();
    });

    document.querySelectorAll('.thumb-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const src = this.dataset.src;
            document.getElementById('main-image').src = src;
            document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('border-indigo-500'));
            this.classList.add('border-indigo-500');
        });
    });

    document.getElementById('zoom-close-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        closeZoom();
    });
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= htmlspecialchars($product['name']) ?>",
    "description": "<?= htmlspecialchars($product['short_description'] ?: $product['description'] ?: '') ?>",
    "sku": "<?= htmlspecialchars($product['sku'] ?? '') ?>",
    "brand": {
        "@type": "Brand",
        "name": "<?= htmlspecialchars($product['brand_name'] ?? '') ?>"
    },
    "offers": {
        "@type": "Offer",
        "price": "<?= $product['price'] ?>",
        "priceCurrency": "MXN",
        "availability": "<?= (int)$product['stock'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' ?>"
    },
    "image": "<?= !empty($images) ? htmlspecialchars(cloudinary_url($images[0]['path'], ['width' => 800, 'quality' => 'auto', 'fetch_format' => 'auto'])) : '' ?>"
}
</script>
