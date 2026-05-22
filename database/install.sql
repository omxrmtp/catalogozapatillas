SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `brands` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `logo_url` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_brands_name` (`name`),
    INDEX `idx_brands_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `parent_id` INT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_categories_name` (`name`),
    INDEX `idx_categories_parent` (`parent_id`),
    INDEX `idx_categories_is_active` (`is_active`),
    CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `short_description` VARCHAR(300) NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `compare_price` DECIMAL(10, 2) NULL,
    `sku` VARCHAR(50) NULL UNIQUE,
    `stock` INT NOT NULL DEFAULT 0,
    `category_id` INT UNSIGNED NULL,
    `brand_id` INT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `meta_title` VARCHAR(200) NULL,
    `meta_description` VARCHAR(300) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_products_name` (`name`),
    INDEX `idx_products_category` (`category_id`),
    INDEX `idx_products_brand` (`brand_id`),
    INDEX `idx_products_is_active` (`is_active`),
    INDEX `idx_products_is_featured` (`is_featured`),
    INDEX `idx_products_price` (`price`),
    FULLTEXT INDEX `ft_products_search` (`name`, `description`, `short_description`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `path_webp` VARCHAR(255) NULL,
    `path_avif` VARCHAR(255) NULL,
    `alt_text` VARCHAR(200) NULL,
    `type` ENUM('thumbnail', 'medium', 'large') NOT NULL DEFAULT 'medium',
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_product_images_product` (`product_id`),
    INDEX `idx_product_images_primary` (`product_id`, `is_primary`),
    CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_sizes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `size` VARCHAR(20) NOT NULL,
    `stock` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_product_size` (`product_id`, `size`),
    INDEX `idx_product_sizes_product` (`product_id`),
    CONSTRAINT `fk_product_sizes_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin', 'editor') NOT NULL DEFAULT 'editor',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(128) PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` TEXT NULL,
    `last_activity` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_sessions_user` (`user_id`),
    INDEX `idx_sessions_last_activity` (`last_activity`),
    CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_audit_log_user` (`user_id`),
    INDEX `idx_audit_log_action` (`action`),
    INDEX `idx_audit_log_entity` (`entity_type`, `entity_id`),
    INDEX `idx_audit_log_created` (`created_at`),
    CONSTRAINT `fk_audit_log_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `email` VARCHAR(150) NULL,
    `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_success` TINYINT(1) NOT NULL DEFAULT 0,
    INDEX `idx_login_attempts_ip` (`ip_address`, `attempted_at`),
    INDEX `idx_login_attempts_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW `v_top_products` AS
SELECT p.id, p.name, p.slug, p.price, p.views_count, b.name AS brand_name, c.name AS category_name
FROM products p
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.is_active = 1
ORDER BY p.views_count DESC
LIMIT 10;

CREATE OR REPLACE VIEW `v_low_stock_products` AS
SELECT p.id, p.name, p.sku, p.stock, b.name AS brand_name
FROM products p
LEFT JOIN brands b ON p.brand_id = b.id
WHERE p.is_active = 1 AND p.stock > 0 AND p.stock <= 5
ORDER BY p.stock ASC;

CREATE OR REPLACE VIEW `v_dashboard_kpis` AS
SELECT
    (SELECT COUNT(*) FROM products WHERE is_active = 1) AS total_active_products,
    (SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock <= 5) AS low_stock_count,
    (SELECT COUNT(*) FROM categories WHERE is_active = 1) AS active_categories,
    (SELECT COUNT(*) FROM brands WHERE is_active = 1) AS active_brands,
    (SELECT COUNT(*) FROM users WHERE is_active = 1) AS active_users,
    (SELECT COALESCE(SUM(stock), 0) FROM products WHERE is_active = 1) AS total_stock;

CREATE OR REPLACE VIEW `v_products_by_category` AS
SELECT c.id, c.name, COUNT(p.id) AS product_count
FROM categories c
LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
WHERE c.is_active = 1
GROUP BY c.id, c.name
ORDER BY product_count DESC;

INSERT INTO `brands` (`name`, `slug`, `description`, `is_active`) VALUES
('Nike', 'nike', 'Marca líder en calzado deportivo.', 1),
('Adidas', 'adidas', 'Innovación y estilo desde Alemania.', 1),
('New Balance', 'new-balance', 'Comodidad y calidad estadounidense.', 1),
('Puma', 'puma', 'Deportivo y casual.', 1),
('Reebok', 'reebok', 'Clásico y funcional.', 1),
('Vans', 'vans', 'Estilo urbano y skate.', 1),
('Converse', 'converse', 'Icono del calzado casual.', 1),
('Asics', 'asics', 'Tecnología en running.', 1);

INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
('Running', 'running', 'Zapatillas para correr.', 1, 1),
('Casual', 'casual', 'Zapatillas para uso diario.', 2, 1),
('Deportivo', 'deportivo', 'Calzado para entrenamiento.', 3, 1),
('Skate', 'skate', 'Diseñadas para skateboarding.', 4, 1),
('Botas', 'botas', 'Botas y calzado de altura.', 5, 1),
('Sandalias', 'sandalias', 'Calzado abierto y fresco.', 6, 1);

INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`) VALUES
('Super Admin', 'admin@catalogozapatillas.com', '$2y$10$ZLBhazxz0YsdQLPZni.YKuMYCUibdawTSI.eqY3sRs96cqQuOvbFC', 'super_admin', 1);

SET FOREIGN_KEY_CHECKS = 1;
