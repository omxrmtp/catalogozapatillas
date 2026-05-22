CREATE TABLE IF NOT EXISTS login_attempts (
    id BIGSERIAL PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(150),
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_success SMALLINT NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts (ip_address, attempted_at);
CREATE INDEX IF NOT EXISTS idx_login_attempts_time ON login_attempts (attempted_at);
