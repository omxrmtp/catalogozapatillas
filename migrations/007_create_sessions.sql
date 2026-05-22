CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_sessions_user ON sessions (user_id);
CREATE INDEX IF NOT EXISTS idx_sessions_last_activity ON sessions (last_activity);
