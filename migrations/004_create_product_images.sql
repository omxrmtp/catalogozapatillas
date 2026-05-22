CREATE TABLE IF NOT EXISTS product_images (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    path VARCHAR(255) NOT NULL,
    path_webp VARCHAR(255),
    path_avif VARCHAR(255),
    alt_text VARCHAR(200),
    type VARCHAR(20) NOT NULL DEFAULT 'medium' CHECK (type IN ('thumbnail', 'medium', 'large')),
    is_primary SMALLINT NOT NULL DEFAULT 0,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_product_images_product ON product_images (product_id);
CREATE INDEX IF NOT EXISTS idx_product_images_primary ON product_images (product_id, is_primary);
