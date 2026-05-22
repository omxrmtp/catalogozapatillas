<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class AuditLog
{
    public function __construct(private readonly Database $db) {}

    public function record(int $userId, string $action, string $entityType, ?int $entityId = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        $this->db->insert('audit_log', [
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_values'  => $oldValues ? json_encode($oldValues) : null,
            'new_values'  => $newValues ? json_encode($newValues) : null,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    public function getRecent(int $limit = 50): array
    {
        return $this->db->fetchAll(
            'SELECT al.*, u.name as user_name
             FROM audit_log al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getByEntity(string $entityType, int $entityId, int $limit = 20): array
    {
        return $this->db->fetchAll(
            'SELECT al.*, u.name as user_name
             FROM audit_log al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE al.entity_type = ? AND al.entity_id = ?
             ORDER BY al.created_at DESC
             LIMIT ?',
            [$entityType, $entityId, $limit]
        );
    }
}
