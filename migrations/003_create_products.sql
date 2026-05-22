CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(300),
    price DECIMAL(10, 2) NOT NULL,
    compare_price DECIMAL(10, 2),
    sku VARCHAR(50) UNIQUE,
    stock INTEGER NOT NULL DEFAULT 0,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE,
    brand_id INTEGER REFERENCES brands(id) ON DELETE SET NULL ON UPDATE CASCADE,
    is_active SMALLINT NOT NULL DEFAULT 1,
    is_featured SMALLINT NOT NULL DEFAULT 0,
    views_count INTEGER NOT NULL DEFAULT 0,
    meta_title VARCHAR(200),
    meta_description VARCHAR(300),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_products_name ON products (name);
CREATE INDEX IF NOT EXISTS idx_products_category ON products (category_id);
CREATE INDEX IF NOT EXISTS idx_products_brand ON products (brand_id);
CREATE INDEX IF NOT EXISTS idx_products_is_active ON products (is_active);
CREATE INDEX IF NOT EXISTS idx_products_is_featured ON products (is_featured);
CREATE INDEX IF NOT EXISTS idx_products_price ON products (price);
