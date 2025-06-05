-- SQL script to create product specification tables

-- Create product_specifications table if it doesn't exist
CREATE TABLE IF NOT EXISTS product_specifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    value TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create product_colors table if it doesn't exist
CREATE TABLE IF NOT EXISTS product_colors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    color_code VARCHAR(10) NULL,
    image VARCHAR(255) NULL,
    price_adjustment DECIMAL(10, 2) DEFAULT 0,
    stock INT DEFAULT 0,
    display_order INT DEFAULT 0,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create product_sizes table if it doesn't exist
CREATE TABLE IF NOT EXISTS product_sizes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    value VARCHAR(255) NULL,
    price_adjustment DECIMAL(10, 2) DEFAULT 0,
    stock INT DEFAULT 0,
    display_order INT DEFAULT 0,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create product_branches table if it doesn't exist
CREATE TABLE IF NOT EXISTS product_branches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    branch_id BIGINT UNSIGNED NOT NULL,
    stock INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    price DECIMAL(10, 2) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE(product_id, branch_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
);

-- Add is_multi_branch column to products table if it doesn't exist
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_multi_branch BOOLEAN DEFAULT FALSE AFTER is_available;

-- Add migration records to the migrations table
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2025_07_15_000001_create_product_specifications_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations m)),
('2025_07_15_000002_create_service_specifications_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations m)),
('2025_07_15_000003_create_product_branches_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations m)),
('2025_08_01_000001_create_product_specifications_tables', (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations m));
