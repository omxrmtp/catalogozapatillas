<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    public function __construct(private readonly Database $db) {}

    public function getAllActive(): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC'
        );
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM categories ORDER BY sort_order ASC, name ASC'
        );
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM categories WHERE id = ?',
            [$id]
        );
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM categories WHERE slug = ?',
            [$slug]
        );
    }

    public function getWithProductCount(): array
    {
        return $this->db->fetchAll(
            'SELECT c.*, COUNT(p.id) as product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
             WHERE c.is_active = 1
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.name ASC'
        );
    }

    public function create(array $data): int
    {
        return $this->db->insert('categories', [
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?? null,
            'parent_id'   => $data['parent_id'] ?? null,
            'is_active'   => $data['is_active'] ?? 1,
            'sort_order'  => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('categories', $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('categories', 'id = ?', [$id]);
    }
}
