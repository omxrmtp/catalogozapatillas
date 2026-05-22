<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Product
{
    public function __construct(private readonly Database $db) {}

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ?',
            [$id]
        );
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            'SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.slug = ? AND p.is_active = 1',
            [$slug]
        );
    }

    public function getFeatured(int $limit = 8): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, b.name as brand_name, b.slug as brand_slug,
                    (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.is_active = 1 AND p.is_featured = 1
             ORDER BY p.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getLatest(int $limit = 8): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, b.name as brand_name, b.slug as brand_slug,
                    (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getAllActive(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = 'WHERE p.is_active = 1';
        $params = [];

        if (!empty($filters['category'])) {
            $where .= ' AND c.slug = ?';
            $params[] = $filters['category'];
        }

        if (!empty($filters['brand'])) {
            $where .= ' AND b.slug = ?';
            $params[] = $filters['brand'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['min_price'])) {
            $where .= ' AND p.price >= ?';
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where .= ' AND p.price <= ?';
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['size'])) {
            $where .= ' AND EXISTS (SELECT 1 FROM product_sizes ps WHERE ps.product_id = p.id AND ps.size = ? AND ps.stock > 0)';
            $params[] = $filters['size'];
        }

        $countSql = "SELECT COUNT(*) FROM products p
                     LEFT JOIN brands b ON p.brand_id = b.id
                     LEFT JOIN categories c ON p.category_id = c.id
                     {$where}";
        $total = $this->db->fetchOne($countSql, $params);
        $totalCount = (int)($total ? reset($total) : 0);

        $offset = ($page - 1) * $perPage;
        $orderBy = $filters['sort'] ?? 'p.created_at DESC';

        $sql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug,
                       (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT GROUP_CONCAT(ps.size ORDER BY ps.size ASC SEPARATOR ', ') FROM product_sizes ps WHERE ps.product_id = p.id) as sizes
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                {$where}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        return [
            'items' => $this->db->fetchAll($sql, $params),
            'total' => $totalCount,
            'page'  => $page,
            'pages' => (int)ceil($totalCount / $perPage),
        ];
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE slug = ?';
        $params = [$slug];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int) reset($result) > 0 : false;
    }

    public function getImages(int $productId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC',
            [$productId]
        );
    }

    public function getSizes(int $productId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM product_sizes WHERE product_id = ? ORDER BY size ASC',
            [$productId]
        );
    }

    public function create(array $data): int
    {
        return $this->db->insert('products', [
            'name'              => $data['name'],
            'slug'              => $data['slug'],
            'description'       => $data['description'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'price'             => $data['price'],
            'compare_price'     => $data['compare_price'] ?? null,
            'sku'               => $data['sku'] ?? null,
            'stock'             => $data['stock'] ?? 0,
            'category_id'       => $data['category_id'] ?? null,
            'brand_id'          => $data['brand_id'] ?? null,
            'is_active'         => $data['is_active'] ?? 1,
            'is_featured'       => $data['is_featured'] ?? 0,
            'meta_title'        => $data['meta_title'] ?? null,
            'meta_description'  => $data['meta_description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('products', $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('products', 'id = ?', [$id]);
    }

    public function saveSize(int $productId, string $size, int $stock): void
    {
        $this->db->insert('product_sizes', [
            'product_id' => $productId,
            'size'       => $size,
            'stock'      => $stock,
        ]);
    }

    public function deleteSizes(int $productId): void
    {
        $this->db->delete('product_sizes', 'product_id = ?', [$productId]);
    }

    public function saveImage(int $productId, array $paths, string $type, bool $isPrimary = false, string $altText = null): int
    {
        return $this->db->insert('product_images', [
            'product_id' => $productId,
            'path'       => $paths['jpg'] ?? '',
            'path_webp'  => $paths['webp'] ?? null,
            'path_avif'  => $paths['avif'] ?? null,
            'type'       => $type,
            'is_primary' => $isPrimary ? 1 : 0,
            'alt_text'   => $altText,
        ]);
    }

    public function deleteImage(int $imageId): void
    {
        $this->db->delete('product_images', 'id = ?', [$imageId]);
    }

    public function deleteProductImages(int $productId): void
    {
        $this->db->delete('product_images', 'product_id = ?', [$productId]);
    }

    public function incrementViews(int $id): void
    {
        $this->db->query(
            'UPDATE products SET views_count = views_count + 1 WHERE id = ?',
            [$id]
        );
    }

    public function search(string $query, int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, b.name as brand_name,
                    (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.is_active = 1
               AND (p.name LIKE ? OR p.description LIKE ? OR b.name LIKE ?)
             LIMIT ?',
            ["%{$query}%", "%{$query}%", "%{$query}%", $limit]
        );
    }

    public function getAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = 'WHERE 1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $where .= ' AND (p.name LIKE ? OR p.sku LIKE ?)';
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        if (isset($filters['is_active'])) {
            $where .= ' AND p.is_active = ?';
            $params[] = $filters['is_active'];
        }

        if (!empty($filters['category_id'])) {
            $where .= ' AND p.category_id = ?';
            $params[] = $filters['category_id'];
        }

        $countSql = "SELECT COUNT(*) FROM products p {$where}";
        $total = $this->db->fetchOne($countSql, $params);
        $totalCount = (int)($total ? reset($total) : 0);

        $offset = ($page - 1) * $perPage;
        $orderBy = $filters['sort'] ?? 'p.created_at DESC';

        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name,
                       (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                {$where}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        return [
            'items' => $this->db->fetchAll($sql, $params),
            'total' => $totalCount,
            'page'  => $page,
            'pages' => (int)ceil($totalCount / $perPage),
        ];
    }

    public function getLowStock(int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, b.name as brand_name
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.is_active = 1 AND p.stock > 0 AND p.stock <= 5
             ORDER BY p.stock ASC
             LIMIT ?',
            [$limit]
        );
    }

    public function getKpis(): array
    {
        return $this->db->fetchOne('SELECT * FROM v_dashboard_kpis') ?? [
            'total_active_products' => 0,
            'low_stock_count' => 0,
            'active_categories' => 0,
            'active_brands' => 0,
            'active_users' => 0,
            'total_stock' => 0,
        ];
    }

    public function getProductsByCategory(): array
    {
        return $this->db->fetchAll('SELECT * FROM v_products_by_category');
    }

    public function getTopProducts(int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM v_top_products LIMIT ?',
            [$limit]
        );
    }
}
