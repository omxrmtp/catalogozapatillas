<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Models\Category;
use App\Models\Product;

final class HomeController extends BaseController
{
    public function index(): void
    {
        $db = Database::getInstance();
        $productModel = new Product($db);
        $categoryModel = new Category($db);

        $featuredProducts = $productModel->getFeatured(8);
        $latestProducts = $productModel->getLatest(8);
        $categories = $categoryModel->getWithProductCount();

        $this->render('home/index', [
            'featuredProducts' => $featuredProducts,
            'latestProducts'   => $latestProducts,
            'categories'       => $categories,
            'metaTitle'        => 'Inicio | Catálogo de Zapatillas',
            'metaDescription'  => 'Explora nuestra colección de zapatillas de las mejores marcas.',
        ]);
    }
}
