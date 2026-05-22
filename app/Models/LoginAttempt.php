<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class LoginAttempt
{
    public function __construct(private readonly Database $db) {}

    public function record(string $ipAddress, ?string $email = null, bool $isSuccess = false): void
    {
        $this->db->insert('login_attempts', [
            'ip_address' => $ipAddress,
            'email'      => $email,
            'is_success' => $isSuccess ? 1 : 0,
        ]);
    }

    public function isRateLimited(string $ipAddress, int $maxAttempts = 5, int $windowSeconds = 900): bool
    {
        $threshold = date('Y-m-d H:i:s', time() - $windowSeconds);
        $result = $this->db->fetchOne(
            'SELECT COUNT(*) as attempts
             FROM login_attempts
             WHERE ip_address = ? AND attempted_at > ? AND is_success = 0',
            [$ipAddress, $threshold]
        );

        return $result && (int)$result['attempts'] >= $maxAttempts;
    }

    public function clearAttempts(string $ipAddress): void
    {
        $this->db->delete('login_attempts', 'ip_address = ? AND is_success = 0', [$ipAddress]);
    }
}
