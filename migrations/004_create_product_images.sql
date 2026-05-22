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
