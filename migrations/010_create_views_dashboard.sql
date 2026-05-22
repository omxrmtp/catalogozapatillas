-- Vista: Top productos más vistos
CREATE OR REPLACE VIEW v_top_products AS
SELECT p.id, p.name, p.slug, p.price, p.views_count, b.name AS brand_name, c.name AS category_name
FROM products p
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.is_active = 1
ORDER BY p.views_count DESC
LIMIT 10;

-- Vista: Productos con stock bajo
CREATE OR REPLACE VIEW v_low_stock_products AS
SELECT p.id, p.name, p.sku, p.stock, b.name AS brand_name
FROM products p
LEFT JOIN brands b ON p.brand_id = b.id
WHERE p.is_active = 1 AND p.stock > 0 AND p.stock <= 5
ORDER BY p.stock ASC;

-- Vista: KPIs del dashboard
CREATE OR REPLACE VIEW v_dashboard_kpis AS
SELECT
    (SELECT COUNT(*) FROM products WHERE is_active = 1) AS total_active_products,
    (SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock <= 5) AS low_stock_count,
    (SELECT COUNT(*) FROM categories WHERE is_active = 1) AS active_categories,
    (SELECT COUNT(*) FROM brands WHERE is_active = 1) AS active_brands,
    (SELECT COUNT(*) FROM users WHERE is_active = 1) AS active_users,
    (SELECT COALESCE(SUM(stock), 0) FROM products WHERE is_active = 1) AS total_stock;

-- Vista: Productos por categoría (para gráficos)
CREATE OR REPLACE VIEW v_products_by_category AS
SELECT c.id, c.name, COUNT(p.id) AS product_count
FROM categories c
LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
WHERE c.is_active = 1
GROUP BY c.id, c.name
ORDER BY product_count DESC;
