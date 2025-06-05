-- Database Recovery SQL Script
-- This script restores the comprehensive categories that were lost

-- First, create parent categories
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) VALUES 
('Clothes', 'Women\'s clothing and apparel', 'product', NULL, 1, 'fas fa-tshirt', NOW(), NOW()),
('Ethnic & Traditional Wear', 'Traditional and ethnic clothing', 'product', NULL, 1, 'fas fa-user-tie', NOW(), NOW()),
('Footwear', 'Shoes and footwear for all occasions', 'product', NULL, 1, 'fas fa-shoe-prints', NOW(), NOW()),
('Accessories', 'Fashion accessories and add-ons', 'product', NULL, 1, 'fas fa-glasses', NOW(), NOW()),
('Bags', 'Handbags, purses and carrying bags', 'product', NULL, 1, 'fas fa-shopping-bag', NOW(), NOW()),
('Jewelry', 'Fashion jewelry and accessories', 'product', NULL, 1, 'fas fa-gem', NOW(), NOW()),
('Makeup', 'Cosmetics and beauty products', 'product', NULL, 1, 'fas fa-palette', NOW(), NOW()),
('Skincare', 'Skincare and beauty treatments', 'product', NULL, 1, 'fas fa-spa', NOW(), NOW()),
('Haircare', 'Hair care and styling products', 'product', NULL, 1, 'fas fa-cut', NOW(), NOW()),
('Hair Accessories', 'Hair styling accessories', 'product', NULL, 1, 'fas fa-ribbon', NOW(), NOW()),
('Fragrances', 'Perfumes and body fragrances', 'product', NULL, 1, 'fas fa-spray-can', NOW(), NOW()),
('Intimates', 'Undergarments and intimate apparel', 'product', NULL, 1, 'fas fa-heart', NOW(), NOW()),
('Maternity Essentials', 'Products for expecting and new mothers', 'product', NULL, 1, 'fas fa-baby', NOW(), NOW()),
('Baby Clothing', 'Clothing for babies and toddlers', 'product', NULL, 1, 'fas fa-baby-carriage', NOW(), NOW()),
('Baby Gear', 'Essential baby equipment and gear', 'product', NULL, 1, 'fas fa-child', NOW(), NOW()),
('Feeding', 'Baby feeding essentials', 'product', NULL, 1, 'fas fa-baby-bottle', NOW(), NOW()),
('Watches', 'Timepieces and smart watches', 'product', NULL, 1, 'fas fa-clock', NOW(), NOW());

-- Now create subcategories for Clothes
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Activewear', 'Sports and fitness clothing', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Bottoms (jeans, skirts)', 'Pants, jeans, skirts and bottom wear', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Dresses', 'Casual and formal dresses', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Loungewear', 'Comfortable home and leisure wear', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Maternity wear', 'Clothing for expecting mothers', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Outerwear (jackets, coats)', 'Jackets, coats and outer garments', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Tops (blouses, tunics)', 'Shirts, blouses and top wear', 'product', id, 1, 'fas fa-tshirt', NOW(), NOW() 
FROM categories WHERE name = 'Clothes' AND type = 'product' AND parent_id IS NULL;

-- Ethnic & Traditional Wear subcategories
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Abayas', 'Traditional Islamic robes', 'product', id, 1, 'fas fa-user-tie', NOW(), NOW() 
FROM categories WHERE name = 'Ethnic & Traditional Wear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Kaftans', 'Loose-fitting traditional dresses', 'product', id, 1, 'fas fa-user-tie', NOW(), NOW() 
FROM categories WHERE name = 'Ethnic & Traditional Wear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Salwar Kameez', 'Traditional South Asian clothing', 'product', id, 1, 'fas fa-user-tie', NOW(), NOW() 
FROM categories WHERE name = 'Ethnic & Traditional Wear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Sarees', 'Traditional Indian garments', 'product', id, 1, 'fas fa-user-tie', NOW(), NOW() 
FROM categories WHERE name = 'Ethnic & Traditional Wear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Pray Clothes', 'Religious and prayer clothing', 'product', id, 1, 'fas fa-user-tie', NOW(), NOW() 
FROM categories WHERE name = 'Ethnic & Traditional Wear' AND type = 'product' AND parent_id IS NULL;

-- Footwear subcategories
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Boots', 'Ankle boots, knee-high boots', 'product', id, 1, 'fas fa-shoe-prints', NOW(), NOW() 
FROM categories WHERE name = 'Footwear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Flats', 'Flat shoes and ballet flats', 'product', id, 1, 'fas fa-shoe-prints', NOW(), NOW() 
FROM categories WHERE name = 'Footwear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Heels', 'High heels and stilettos', 'product', id, 1, 'fas fa-shoe-prints', NOW(), NOW() 
FROM categories WHERE name = 'Footwear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Sandals', 'Open-toe sandals and flip-flops', 'product', id, 1, 'fas fa-shoe-prints', NOW(), NOW() 
FROM categories WHERE name = 'Footwear' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Sneakers', 'Athletic and casual sneakers', 'product', id, 1, 'fas fa-shoe-prints', NOW(), NOW() 
FROM categories WHERE name = 'Footwear' AND type = 'product' AND parent_id IS NULL;

-- Accessories subcategories
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Belts', 'Leather and fabric belts', 'product', id, 1, 'fas fa-glasses', NOW(), NOW() 
FROM categories WHERE name = 'Accessories' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Hats', 'Caps, hats and headwear', 'product', id, 1, 'fas fa-glasses', NOW(), NOW() 
FROM categories WHERE name = 'Accessories' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Scarves', 'Silk and cotton scarves', 'product', id, 1, 'fas fa-glasses', NOW(), NOW() 
FROM categories WHERE name = 'Accessories' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Sunglasses', 'Designer and casual sunglasses', 'product', id, 1, 'fas fa-glasses', NOW(), NOW() 
FROM categories WHERE name = 'Accessories' AND type = 'product' AND parent_id IS NULL;

-- Bags subcategories
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Backpacks', 'School and travel backpacks', 'product', id, 1, 'fas fa-shopping-bag', NOW(), NOW() 
FROM categories WHERE name = 'Bags' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Crossbody bags', 'Small crossbody and shoulder bags', 'product', id, 1, 'fas fa-shopping-bag', NOW(), NOW() 
FROM categories WHERE name = 'Bags' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Tote bags', 'Large tote and shopping bags', 'product', id, 1, 'fas fa-shopping-bag', NOW(), NOW() 
FROM categories WHERE name = 'Bags' AND type = 'product' AND parent_id IS NULL;

-- Jewelry subcategories
INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Anklets', 'Ankle bracelets and chains', 'product', id, 1, 'fas fa-gem', NOW(), NOW() 
FROM categories WHERE name = 'Jewelry' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Bracelets', 'Wrist bracelets and bangles', 'product', id, 1, 'fas fa-gem', NOW(), NOW() 
FROM categories WHERE name = 'Jewelry' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Earrings', 'Stud, hoop and drop earrings', 'product', id, 1, 'fas fa-gem', NOW(), NOW() 
FROM categories WHERE name = 'Jewelry' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Necklaces', 'Chains, pendants and chokers', 'product', id, 1, 'fas fa-gem', NOW(), NOW() 
FROM categories WHERE name = 'Jewelry' AND type = 'product' AND parent_id IS NULL;

INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, icon, created_at, updated_at) 
SELECT 'Rings', 'Fashion and statement rings', 'product', id, 1, 'fas fa-gem', NOW(), NOW() 
FROM categories WHERE name = 'Jewelry' AND type = 'product' AND parent_id IS NULL;
