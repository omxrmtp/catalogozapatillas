<section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                Encuentra tus <span class="text-yellow-300">zapatillas</span> ideales
            </h1>
            <p class="text-lg md:text-xl text-indigo-100 mb-8">
                Explora nuestra colección de zapatillas de las mejores marcas. Running, casual, deportivo y más.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/catalogo" class="inline-flex items-center justify-center px-8 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-gray-100 transition shadow-lg">
                    Ver catálogo
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($categories)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Categorías</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($categories as $cat): ?>
        <a href="/catalogo?category=<?= htmlspecialchars($cat['slug']) ?>" class="group relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition"><?= htmlspecialchars($cat['name']) ?></h3>
            <p class="text-sm text-gray-500 mt-1"><?= (int)$cat['product_count'] ?> productos</p>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($featuredProducts)): ?>
<section id="featured" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Productos Destacados</h2>
        <a href="/catalogo" class="text-indigo-600 font-medium hover:text-indigo-800 transition">Ver todos →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($featuredProducts as $product): ?>
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
                <h3 class="font-semibold text-gray-900 mt-1 group-hover:text-indigo-600 transition"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-lg font-bold text-gray-900 mt-2">S/. <?= number_format((float)$product['price'], 2) ?></p>
                <?php if ($product['compare_price'] && (float)$product['compare_price'] > (float)$product['price']): ?>
                <p class="text-sm text-gray-500 line-through">S/. <?= number_format((float)$product['compare_price'], 2) ?></p>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($latestProducts)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Últimos agregados</h2>
        <a href="/catalogo" class="text-indigo-600 font-medium hover:text-indigo-800 transition">Ver todos →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($latestProducts as $product): ?>
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
                <h3 class="font-semibold text-gray-900 mt-1 group-hover:text-indigo-600 transition"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-lg font-bold text-gray-900 mt-2">S/. <?= number_format((float)$product['price'], 2) ?></p>
                <?php if ($product['compare_price'] && (float)$product['compare_price'] > (float)$product['price']): ?>
                <p class="text-sm text-gray-500 line-through">S/. <?= number_format((float)$product['compare_price'], 2) ?></p>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
