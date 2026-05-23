<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\ImageProcessor;
use App\Core\Session;
use App\Core\Validator;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

final class AdminController extends BaseController
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;
    private User $userModel;
    private AuditLog $auditLog;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->productModel = new Product($db);
        $this->categoryModel = new Category($db);
        $this->brandModel = new Brand($db);
        $this->userModel = new User($db);
        $this->auditLog = new AuditLog($db);
    }

    public function dashboard(): void
    {
        $this->requireAuth();

        $kpis = $this->productModel->getKpis();
        $productsByCategory = $this->productModel->getProductsByCategory();
        $topProducts = $this->productModel->getTopProducts();
        $lowStock = $this->productModel->getLowStock();
        $recentLogs = $this->auditLog->getRecent(10);

        $this->renderAdmin('admin/dashboard', [
            'kpis'              => $kpis,
            'productsByCategory' => $productsByCategory,
            'topProducts'       => $topProducts,
            'lowStock'          => $lowStock,
            'recentLogs'        => $recentLogs,
            'metaTitle'         => 'Dashboard | Admin',
        ]);
    }

    public function products(): void
    {
        $this->requireAuth();
        $filters = $this->getQueryData();
        $page = max(1, (int)($filters['page'] ?? 1));
        $result = $this->productModel->getAll($filters, $page);
        $categories = $this->categoryModel->getAll();

        $this->renderAdmin('admin/products/index', [
            'products'   => $result['items'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'categories' => $categories,
            'filters'    => $filters,
            'metaTitle'  => 'Productos | Admin',
        ]);
    }

    public function productCreate(): void
    {
        $this->requireAuth();
        $categories = $this->categoryModel->getAllActive();
        $brands = $this->brandModel->getAllActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            $errors = [];

            $data['name'] = trim($data['name'] ?? '');
            if (empty($data['name']) || mb_strlen($data['name']) < 3) {
                $errors[] = 'El nombre debe tener al menos 3 caracteres.';
            }
            if (mb_strlen($data['name']) > 200) {
                $errors[] = 'El nombre no debe exceder 200 caracteres.';
            }

            $data['price'] = str_replace(',', '', trim($data['price'] ?? ''));
            if ($data['price'] === '' || !is_numeric($data['price']) || (float)$data['price'] <= 0) {
                $errors[] = 'El precio debe ser un número mayor a 0.';
            }

            if (!empty($data['compare_price'])) {
                $data['compare_price'] = str_replace(',', '', trim($data['compare_price']));
                if (!is_numeric($data['compare_price']) || (float)$data['compare_price'] < 0) {
                    $errors[] = 'El precio comparativo debe ser un número válido.';
                }
            } else {
                $data['compare_price'] = null;
            }

            if (!empty($data['sku'])) {
                if (mb_strlen($data['sku']) > 50) {
                    $errors[] = 'El SKU no debe exceder 50 caracteres.';
                }
            } else {
                $data['sku'] = null;
            }

            if (empty($data['category_id'])) {
                $data['category_id'] = null;
            }
            if (empty($data['brand_id'])) {
                $data['brand_id'] = null;
            }

            $sizes = $data['sizes'] ?? [];
            unset($data['sizes']);
            $hasValidSize = false;
            foreach ($sizes as $s) {
                if (!empty(trim($s['size'] ?? ''))) {
                    $hasValidSize = true;
                    break;
                }
            }

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                $this->redirectBack();
            }

            try {
                $baseSlug = $this->slugify($data['name']);
                $slug = $baseSlug;
                $counter = 1;
                while ($this->productModel->slugExists($slug)) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $data['slug'] = $slug;
                $data['is_active'] = isset($data['is_active']) ? 1 : 0;
                $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
                $data['description'] = $data['short_description'] ?? null;
                $data['meta_title'] = $data['name'];
                $data['meta_description'] = !empty($data['short_description'])
                    ? mb_substr($data['short_description'], 0, 160)
                    : $data['name'];

                $productId = $this->productModel->create($data);

                if ($hasValidSize) {
                    foreach ($sizes as $s) {
                        if (!empty(trim($s['size'] ?? ''))) {
                            $this->productModel->saveSize($productId, trim($s['size']), max(0, (int)($s['stock'] ?? 0)));
                        }
                    }
                }

                $this->uploadProductImage($productId, $data['slug']);

                $this->auditLog->record(
                    (int)Session::getUserId(),
                    'create',
                    'product',
                    $productId,
                    null,
                    $data
                );

                Session::setFlash('success', 'Producto creado exitosamente.');
                $this->redirect('/admin/productos');
            } catch (\Throwable $e) {
                Session::setFlash('error', 'Error al crear producto: ' . $e->getMessage());
                $this->redirectBack();
            }
        }

        $this->renderAdmin('admin/products/form', [
            'product'    => null,
            'categories' => $categories,
            'brands'     => $brands,
            'sizes'      => [],
            'images'     => [],
            'metaTitle'  => 'Nuevo Producto | Admin',
        ]);
    }

    public function productEdit(int $id): void
    {
        $this->requireAuth();
        $product = $this->productModel->getById($id);

        if (!$product) {
            Session::setFlash('error', 'Producto no encontrado.');
            $this->redirect('/admin/productos');
        }

        $categories = $this->categoryModel->getAllActive();
        $brands = $this->brandModel->getAllActive();
        $images = $this->productModel->getImages($id);
        $sizes = $this->productModel->getSizes($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            unset($data['_csrf_token']);
            $errors = [];

            $data['name'] = trim($data['name'] ?? '');
            if (empty($data['name']) || mb_strlen($data['name']) < 3) {
                $errors[] = 'El nombre debe tener al menos 3 caracteres.';
            }
            if (mb_strlen($data['name']) > 200) {
                $errors[] = 'El nombre no debe exceder 200 caracteres.';
            }

            $data['price'] = str_replace(',', '', trim($data['price'] ?? ''));
            if ($data['price'] === '' || !is_numeric($data['price']) || (float)$data['price'] <= 0) {
                $errors[] = 'El precio debe ser un número mayor a 0.';
            }

            if (!empty($data['compare_price'])) {
                $data['compare_price'] = str_replace(',', '', trim($data['compare_price']));
                if (!is_numeric($data['compare_price']) || (float)$data['compare_price'] < 0) {
                    $errors[] = 'El precio comparativo debe ser un número válido.';
                }
            } else {
                $data['compare_price'] = null;
            }

            if (!empty($data['sku'])) {
                if (mb_strlen($data['sku']) > 50) {
                    $errors[] = 'El SKU no debe exceder 50 caracteres.';
                }
            } else {
                $data['sku'] = null;
            }

            if (empty($data['category_id'])) {
                $data['category_id'] = null;
            }
            if (empty($data['brand_id'])) {
                $data['brand_id'] = null;
            }

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                $this->redirectBack();
            }

            try {
                $baseSlug = $this->slugify($data['name']);
                $slug = $baseSlug;
                $counter = 1;
                while ($this->productModel->slugExists($slug, $id)) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $data['slug'] = $slug;
                $data['is_active'] = isset($data['is_active']) ? 1 : 0;
                $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
                $data['description'] = $data['short_description'] ?? null;
                $data['meta_title'] = $data['name'];
                $data['meta_description'] = !empty($data['short_description'])
                    ? mb_substr($data['short_description'], 0, 160)
                    : $data['name'];

                $sizesForm = $data['sizes'] ?? [];
                unset($data['sizes']);

                $oldValues = $product;
                $this->productModel->update($id, $data);

                $this->productModel->deleteSizes($id);
                foreach ($sizesForm as $s) {
                    if (!empty(trim($s['size'] ?? ''))) {
                        $this->productModel->saveSize($id, trim($s['size']), max(0, (int)($s['stock'] ?? 0)));
                    }
                }

                $this->uploadProductImage($id, $data['slug']);

                $this->auditLog->record(
                    (int)Session::getUserId(),
                    'update',
                    'product',
                    $id,
                    $oldValues,
                    $data
                );

                Session::setFlash('success', 'Producto actualizado exitosamente.');
                $this->redirect('/admin/productos');
            } catch (\Throwable $e) {
                Session::setFlash('error', 'Error al actualizar producto: ' . $e->getMessage());
                $this->redirectBack();
            }
        }

        $this->renderAdmin('admin/products/form', [
            'product'    => $product,
            'categories' => $categories,
            'brands'     => $brands,
            'images'     => $images,
            'sizes'      => $sizes,
            'metaTitle'  => 'Editar Producto | Admin',
        ]);
    }

    public function productToggle(int $id): void
    {
        $this->requireAuth();
        $product = $this->productModel->getById($id);

        if (!$product) {
            $this->json(['error' => 'Producto no encontrado'], 404);
            return;
        }

        $newStatus = $product['is_active'] ? 0 : 1;
        $this->productModel->update($id, ['is_active' => $newStatus]);

        $this->auditLog->record(
            (int)Session::getUserId(),
            $newStatus ? 'activate' : 'deactivate',
            'product',
            $id
        );

        $this->json(['success' => true, 'is_active' => $newStatus]);
    }

    public function productDelete(int $id): void
    {
        $this->requireAuth();
        $product = $this->productModel->getById($id);

        if (!$product) {
            $this->json(['error' => 'Producto no encontrado'], 404);
            return;
        }

        try {
            $this->productModel->deleteSizes($id);
            $this->productModel->delete($id);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'delete',
                'product',
                $id,
                $product
            );

            $this->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Error al eliminar producto: ' . $e->getMessage()], 500);
        }
    }

    public function imageDelete(int $id): void
    {
        $this->requireAuth();
        $image = Database::getInstance()->fetchOne('SELECT * FROM product_images WHERE id = ?', [$id]);

        if (!$image) {
            $this->json(['error' => 'Imagen no encontrada'], 404);
            return;
        }

        if (!empty($image['cloudinary_public_id'])) {
            $cloudinary = new \App\Core\CloudinaryService();
            if ($cloudinary->isConfigured()) {
                try {
                    $cloudinary->delete($image['cloudinary_public_id']);
                } catch (\Throwable) {
                }
            }
        }

        $filesToDelete = array_filter([$image['path'], $image['path_webp'], $image['path_avif']]);
        foreach ($filesToDelete as $file) {
            if (!str_starts_with((string)$file, '/uploads/')) {
                continue;
            }
            $fullPath = PUBLIC_PATH . $file;
            if (file_exists($fullPath) && is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        $this->productModel->deleteImage($id);
        $this->json(['success' => true]);
    }

    public function categories(): void
    {
        $this->requireAuth();
        $categories = $this->categoryModel->getAll();

        $this->renderAdmin('admin/categories/index', [
            'categories' => $categories,
            'metaTitle'  => 'Categorías | Admin',
        ]);
    }

    public function categoryCreate(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            $data['slug'] = $this->slugify($data['name']);
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;

            $categoryId = $this->categoryModel->create($data);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'create',
                'category',
                $categoryId,
                null,
                $data
            );

            Session::setFlash('success', 'Categoría creada exitosamente.');
            $this->redirect('/admin/categorias');
        }
    }

    public function categoryEdit(int $id): void
    {
        $this->requireAuth();
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            Session::setFlash('error', 'Categoría no encontrada.');
            $this->redirect('/admin/categorias');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            $data['slug'] = $this->slugify($data['name']);
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;

            $this->categoryModel->update($id, $data);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'update',
                'category',
                $id,
                $category,
                $data
            );

            Session::setFlash('success', 'Categoría actualizada.');
            $this->redirect('/admin/categorias');
        }
    }

    public function brands(): void
    {
        $this->requireAuth();
        $brands = $this->brandModel->getAll();

        $this->renderAdmin('admin/brands/index', [
            'brands'    => $brands,
            'metaTitle' => 'Marcas | Admin',
        ]);
    }

    public function brandCreate(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            $data['slug'] = $this->slugify($data['name']);
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;

            $brandId = $this->brandModel->create($data);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'create',
                'brand',
                $brandId,
                null,
                $data
            );

            Session::setFlash('success', 'Marca creada exitosamente.');
            $this->redirect('/admin/marcas');
        }
    }

    public function brandEdit(int $id): void
    {
        $this->requireAuth();
        $brand = $this->brandModel->getById($id);

        if (!$brand) {
            Session::setFlash('error', 'Marca no encontrada.');
            $this->redirect('/admin/marcas');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            $data['slug'] = $this->slugify($data['name']);
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;

            $this->brandModel->update($id, $data);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'update',
                'brand',
                $id,
                $brand,
                $data
            );

            Session::setFlash('success', 'Marca actualizada.');
            $this->redirect('/admin/marcas');
        }
    }

    public function users(): void
    {
        $this->requireSuperAdmin();
        $users = $this->userModel->getAll();

        $this->renderAdmin('admin/users/index', [
            'users'     => $users,
            'metaTitle' => 'Usuarios | Admin',
        ]);
    }

    public function userCreate(): void
    {
        $this->requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            $validator = new Validator();

            if (!$validator->validate([
                'name'     => ['required' => true, 'min_length' => 3, 'label' => 'Nombre'],
                'email'    => ['required' => true, 'email' => true, 'label' => 'Correo'],
                'password' => ['required' => true, 'min_length' => 8, 'label' => 'Contraseña'],
            ], $data)) {
                Session::setFlash('error', $validator->getFirstError());
                $this->redirectBack();
            }

            $userId = $this->userModel->create($data);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'create',
                'user',
                $userId,
                null,
                ['name' => $data['name'], 'email' => $data['email'], 'role' => $data['role'] ?? 'editor']
            );

            Session::setFlash('success', 'Usuario creado exitosamente.');
            $this->redirect('/admin/usuarios');
        }

        $this->renderAdmin('admin/users/form', [
            'user'      => null,
            'metaTitle' => 'Nuevo Usuario | Admin',
        ]);
    }

    public function userEdit(int $id): void
    {
        $this->requireSuperAdmin();
        $user = $this->userModel->getById($id);

        if (!$user) {
            Session::setFlash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/usuarios');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $this->redirectBack();
            }

            $data = $this->getPostData();
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $this->userModel->update($id, $data);

            $this->auditLog->record(
                (int)Session::getUserId(),
                'update',
                'user',
                $id,
                $user,
                $data
            );

            Session::setFlash('success', 'Usuario actualizado.');
            $this->redirect('/admin/usuarios');
        }

        $this->renderAdmin('admin/users/form', [
            'user'      => $user,
            'metaTitle' => 'Editar Usuario | Admin',
        ]);
    }

    public function auditLogView(): void
    {
        $this->requireSuperAdmin();
        $logs = $this->auditLog->getRecent(100);

        $this->renderAdmin('admin/audit_log', [
            'logs'      => $logs,
            'metaTitle' => 'Auditoría | Admin',
        ]);
    }

    private function uploadProductImage(int $productId, string $slug): void
    {
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return;
        }

        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 100 * 1024 * 1024;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes, true)) {
            Session::setFlash('error', 'Tipo de imagen no permitido. Solo JPG, PNG o WEBP.');
            return;
        }

        if ($file['size'] > $maxSize) {
            Session::setFlash('error', 'La imagen excede el tamaño máximo de 100 MB.');
            return;
        }

        $this->deleteExistingProductImages($productId, $slug);

        try {
            $processor = new ImageProcessor();
            $images = $processor->process($file, $slug);

            $this->productModel->saveImage(
                $productId,
                $images['medium'],
                'medium',
                true,
                $slug,
                $images['public_id'] ?? null
            );
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Error al procesar la imagen: ' . $e->getMessage());
        }
    }

    private function deleteExistingProductImages(int $productId, string $slug): void
    {
        $existing = $this->productModel->getImages($productId);
        $cloudinary = new \App\Core\CloudinaryService();

        foreach ($existing as $img) {
            if (!empty($img['cloudinary_public_id']) && $cloudinary->isConfigured()) {
                try {
                    $cloudinary->delete($img['cloudinary_public_id']);
                } catch (\Throwable) {
                }
            }

            $filesToDelete = array_filter([$img['path'], $img['path_webp'], $img['path_avif']]);
            foreach ($filesToDelete as $file) {
                if (!str_starts_with((string)$file, '/uploads/')) {
                    continue;
                }
                $fullPath = PUBLIC_PATH . $file;
                if (file_exists($fullPath) && is_file($fullPath)) {
                    unlink($fullPath);
                }
            }
        }
        $this->productModel->deleteProductImages($productId);

        $dir = PUBLIC_PATH . '/uploads/' . $slug;
        if (is_dir($dir)) {
            $remaining = glob($dir . '/*');
            if (empty($remaining)) {
                rmdir($dir);
            }
        }
    }
}
