<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

final class ProductController extends BaseController
{
    public function catalog(): void
    {
        $db = Database::getInstance();
        $productModel = new Product($db);
        $categoryModel = new Category($db);
        $brandModel = new Brand($db);

        $filters = $this->getQueryData();
        $page = max(1, (int)($filters['page'] ?? 1));

        $result = $productModel->getAllActive($filters, $page);
        $categories = $categoryModel->getWithProductCount();
        $brands = $brandModel->getAllActive();

        $this->render('products/catalog', [
            'products'   => $result['items'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'categories' => $categories,
            'brands'     => $brands,
            'filters'    => $filters,
            'metaTitle'  => 'Catálogo | Catálogo de Zapatillas',
            'metaDescription' => 'Descubre todas nuestras zapatillas disponibles.',
        ]);
    }

    public function detail(string $slug): void
    {
        $db = Database::getInstance();
        $productModel = new Product($db);

        $product = $productModel->getBySlug($slug);

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', [], 'main');
            return;
        }

        $productModel->incrementViews((int)$product['id']);

        $images = $productModel->getImages((int)$product['id']);
        $sizes = $productModel->getSizes((int)$product['id']);

        $metaTitle = $product['meta_title'] ?: $product['name'] . ' | Catálogo de Zapatillas';
        $metaDescription = $product['meta_description'] ?: "{$product['name']} - {$product['brand_name']}. Descubre más en nuestro catálogo.";

        $this->render('products/detail', [
            'product' => $product,
            'images'  => $images,
            'sizes'   => $sizes,
            'metaTitle'       => $metaTitle,
            'metaDescription' => $metaDescription,
            'ogImage'         => $images[0]['path'] ?? null,
        ]);
    }

    public function searchAjax(): void
    {
        $query = $this->getQueryData()['q'] ?? '';

        if (mb_strlen($query) < 2) {
            $this->json([]);
            return;
        }

        $db = Database::getInstance();
        $productModel = new Product($db);
        $results = $productModel->search($query);

        $this->json($results);
    }
}
