<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Brand
{
    public function __construct(private readonly Database $db) {}

    public function getAllActive(): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC'
        );
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM brands ORDER BY name ASC'
        );
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM brands WHERE id = ?',
            [$id]
        );
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM brands WHERE slug = ?',
            [$slug]
        );
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $id = $this->db->insert('brands', [
                'name'        => $data['name'],
                'slug'        => $data['slug'],
                'description' => $data['description'] ?? null,
                'logo_url'    => $data['logo_url'] ?? null,
                'is_active'   => $data['is_active'] ?? 1,
            ]);
            $this->db->commit();
            return $id;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('brands', $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('brands', 'id = ?', [$id]);
    }
}
