-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 06:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marketplace_windsurf`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `booking_number` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 60,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `emirate` varchar(255) DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `branch_image` varchar(255) DEFAULT NULL,
  `use_company_image` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_ratings` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `opening_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opening_hours`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `popularity_score` int(11) NOT NULL DEFAULT 0,
  `last_score_calculation` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `user_id`, `company_id`, `name`, `address`, `emirate`, `lat`, `lng`, `status`, `featured`, `image`, `branch_image`, `use_company_image`, `description`, `rating`, `average_rating`, `total_ratings`, `phone`, `email`, `opening_hours`, `created_at`, `updated_at`, `view_count`, `order_count`, `popularity_score`, `last_score_calculation`) VALUES
(1, 2, 1, 'Downtown Tech Hub', '123 Main Street, San Francisco, CA 94105', NULL, 37.77490000, -122.41940000, 'active', 0, '/images/placeholder.jpg', NULL, 1, 'Our flagship technology center in the heart of downtown.', 4.80, 0.00, 0, '1234567890', 'downtown@techsolutions.example.com', '{\"monday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"tuesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"wednesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"thursday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"friday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"saturday\":{\"is_open\":true,\"open\":\"10:00\",\"close\":\"16:00\"},\"sunday\":{\"is_open\":false,\"open\":null,\"close\":null}}', '2025-06-03 03:19:30', '2025-06-03 03:19:30', 0, 0, 0, NULL),
(2, 2, 1, 'Silicon Valley Office', '456 Innovation Drive, Palo Alto, CA 94301', NULL, 37.44190000, -122.14300000, 'active', 0, '/images/placeholder.jpg', NULL, 1, 'Our Silicon Valley branch specializing in cutting-edge research.', 4.70, 0.00, 0, '1234567891', 'valley@techsolutions.example.com', '{\"monday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"tuesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"wednesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"thursday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"friday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"saturday\":{\"is_open\":true,\"open\":\"10:00\",\"close\":\"16:00\"},\"sunday\":{\"is_open\":false,\"open\":null,\"close\":null}}', '2025-06-03 03:19:30', '2025-06-03 03:19:30', 0, 0, 0, NULL),
(3, 3, 2, 'Beverly Hills Wellness', '789 Relaxation Ave, Beverly Hills, CA 90210', NULL, 34.07360000, -118.40040000, 'active', 0, '/images/placeholder.jpg', NULL, 1, 'Luxury wellness services in the heart of Beverly Hills.', 4.90, 0.00, 0, '2345678901', 'beverly@wellness.example.com', '{\"monday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"tuesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"wednesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"thursday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"friday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"saturday\":{\"is_open\":true,\"open\":\"10:00\",\"close\":\"16:00\"},\"sunday\":{\"is_open\":false,\"open\":null,\"close\":null}}', '2025-06-03 03:19:30', '2025-06-03 03:19:30', 0, 0, 0, NULL),
(4, 3, 2, 'Santa Monica Beach Center', '101 Ocean View Blvd, Santa Monica, CA 90401', NULL, 34.01950000, -118.49120000, 'active', 0, '/images/placeholder.jpg', NULL, 1, 'Beachside wellness center with ocean views and fresh air.', 4.60, 0.00, 0, '2345678902', 'santamonica@wellness.example.com', '{\"monday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"tuesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"wednesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"thursday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"friday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"saturday\":{\"is_open\":true,\"open\":\"10:00\",\"close\":\"16:00\"},\"sunday\":{\"is_open\":false,\"open\":null,\"close\":null}}', '2025-06-03 03:19:30', '2025-06-03 03:19:30', 0, 0, 0, NULL),
(5, 4, 3, 'Manhattan Gourmet', '222 Fifth Avenue, New York, NY 10001', NULL, 40.71280000, -74.00600000, 'active', 0, '/images/placeholder.jpg', NULL, 1, 'Our flagship gourmet store in Manhattan.', 4.70, 0.00, 0, '3456789012', 'manhattan@gourmet.example.com', '{\"monday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"tuesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"wednesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"thursday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"friday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"saturday\":{\"is_open\":true,\"open\":\"10:00\",\"close\":\"16:00\"},\"sunday\":{\"is_open\":false,\"open\":null,\"close\":null}}', '2025-06-03 03:19:30', '2025-06-03 03:19:30', 0, 0, 0, NULL),
(6, 4, 3, 'Brooklyn Artisanal', '333 Williamsburg St, Brooklyn, NY 11211', NULL, 40.71280000, -73.95000000, 'active', 0, '/images/placeholder.jpg', NULL, 1, 'Artisanal food products in trendy Williamsburg.', 4.50, 0.00, 0, '3456789013', 'brooklyn@gourmet.example.com', '{\"monday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"tuesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"wednesday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"thursday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"friday\":{\"is_open\":true,\"open\":\"09:00\",\"close\":\"18:00\"},\"saturday\":{\"is_open\":true,\"open\":\"10:00\",\"close\":\"16:00\"},\"sunday\":{\"is_open\":false,\"open\":null,\"close\":null}}', '2025-06-03 03:19:30', '2025-06-03 03:19:30', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branch_ratings`
--

CREATE TABLE `branch_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5',
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_ratings`
--

INSERT INTO `branch_ratings` (`id`, `customer_id`, `branch_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(2, 11, 5, 3, 'good branch', '2025-05-25 00:06:45', '2025-05-26 00:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL COMMENT 'product or service',
  `default_size_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `purchase_count` int(11) NOT NULL DEFAULT 0,
  `trending_score` int(11) NOT NULL DEFAULT 0,
  `last_trending_calculation` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`, `default_size_category_id`, `description`, `image`, `icon`, `parent_id`, `is_active`, `created_at`, `updated_at`, `view_count`, `purchase_count`, `trending_score`, `last_trending_calculation`) VALUES
(20, 'Clothes', 'product', NULL, 'Women\'s clothing and apparel', '/storage/categories/1748969520_Clothes.jpg', 'fas fa-tshirt', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(21, 'Ethnic & Traditional Wear', 'product', NULL, 'Traditional and ethnic clothing', '/storage/categories/1748969520_Ethnic_&_Traditional_Wear.jpg', 'fas fa-user-tie', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(22, 'Footwear', 'product', NULL, 'Shoes and footwear for all occasions', '/storage/categories/1748969520_Footwear.jpg', 'fas fa-shoe-prints', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(23, 'Accessories', 'product', NULL, 'Fashion accessories and add-ons', '/storage/categories/1748969520_Accessories.jpg', 'fas fa-glasses', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(24, 'Bags', 'product', NULL, 'Handbags, purses and carrying bags', '/storage/categories/1748969520_Bags.jpg', 'fas fa-shopping-bag', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(25, 'Jewelry', 'product', NULL, 'Fashion jewelry and accessories', '/storage/categories/1748969520_Jewelry.jpg', 'fas fa-gem', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(26, 'Makeup', 'product', NULL, 'Cosmetics and beauty products', '/storage/categories/1748969520_Makeup.jpg', 'fas fa-palette', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(27, 'Skincare', 'product', NULL, 'Skincare and beauty treatments', '/storage/categories/1748969520_Skincare.jpg', 'fas fa-spa', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(28, 'Haircare', 'product', NULL, 'Hair care and styling products', '/storage/categories/1748969520_Haircare.jpg', 'fas fa-cut', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(29, 'Hair Accessories', 'product', NULL, 'Hair styling accessories', '/storage/categories/1748969520_Hair_Accessories.jpg', 'fas fa-ribbon', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(30, 'Fragrances', 'product', NULL, 'Perfumes and body fragrances', '/storage/categories/1748969520_Fragrances.jpg', 'fas fa-spray-can', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(31, 'Intimates', 'product', NULL, 'Undergarments and intimate apparel', '/storage/categories/1748969520_Intimates.jpg', 'fas fa-heart', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(32, 'Maternity Essentials', 'product', NULL, 'Products for expecting and new mothers', '/storage/categories/1748969520_Maternity_Essentials.jpg', 'fas fa-baby', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(33, 'Baby Clothing', 'product', NULL, 'Clothing for babies and toddlers', '/storage/categories/1748969520_Baby_Clothing.jpg', 'fas fa-baby-carriage', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(34, 'Baby Gear', 'product', NULL, 'Essential baby equipment and gear', '/storage/categories/1748969520_Baby_Gear.jpg', 'fas fa-child', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(35, 'Feeding', 'product', NULL, 'Baby feeding essentials', '/storage/categories/1748969521_Feeding.jpg', 'fas fa-baby-bottle', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(36, 'Watches', 'product', NULL, 'Timepieces and smart watches', '/storage/categories/1748969521_Watches.jpg', 'fas fa-clock', NULL, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(37, 'Activewear', 'product', NULL, 'Sports and fitness clothing', '/storage/categories/1748969521_Activewear.jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(38, 'Bottoms (jeans, skirts)', 'product', NULL, 'Pants, jeans, skirts and bottom wear', '/storage/categories/1748969521_Bottoms_(jeans,_skirts).jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(39, 'Dresses', 'product', NULL, 'Casual and formal dresses', '/storage/categories/1748969521_Dresses.jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(40, 'Loungewear', 'product', NULL, 'Comfortable home and leisure wear', '/storage/categories/1748969521_Loungewear.jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(41, 'Maternity wear', 'product', NULL, 'Clothing for expecting mothers', '/storage/categories/1748969521_Maternity_wear.jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(42, 'Outerwear (jackets, coats)', 'product', NULL, 'Jackets, coats and outer garments', '/storage/categories/1748969521_Outerwear_(jackets,_coats).jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(43, 'Tops (blouses, tunics)', 'product', NULL, 'Shirts, blouses and top wear', '/storage/categories/1748969521_Tops_(blouses,_tunics).jpg', 'fas fa-tag', 20, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:48', 0, 0, 0, NULL),
(44, 'Abayas', 'product', NULL, 'Traditional Islamic robes', '/storage/categories/1748969521_Abayas.jpg', 'fas fa-tag', 21, 1, '2025-06-03 03:22:58', '2025-06-03 13:41:20', 0, 0, 0, NULL),
(45, 'Kaftans', 'product', NULL, 'Loose-fitting traditional dresses', '/storage/categories/1748969521_Kaftans.jpg', 'fas fa-tag', 21, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(46, 'Salwar Kameez', 'product', NULL, 'Traditional South Asian clothing', '/storage/categories/1748969521_Salwar_Kameez.jpg', 'fas fa-tag', 21, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(47, 'Sarees', 'product', NULL, 'Traditional Indian garments', '/storage/categories/1748969521_Sarees.jpg', 'fas fa-tag', 21, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(48, 'Pray Clothes', 'product', NULL, 'Religious and prayer clothing', 'categories/ES6I16dHewD8TObSO62H24LLgyZZFIg1Uhnacnc5.jpg', 'fas fa-tag', 21, 1, '2025-06-03 03:22:58', '2025-06-03 13:55:13', 0, 0, 0, NULL),
(49, 'Boots', 'product', NULL, 'Ankle boots, knee-high boots', '/storage/categories/1748969521_Boots.jpg', 'fas fa-tag', 22, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(50, 'Flats', 'product', NULL, 'Flat shoes and ballet flats', '/storage/categories/1748969521_Flats.jpg', 'fas fa-tag', 22, 1, '2025-06-03 03:22:58', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(51, 'Heels', 'product', NULL, 'High heels and stilettos', '/storage/categories/1748969521_Heels.jpg', 'fas fa-tag', 22, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(52, 'Sandals', 'product', NULL, 'Open-toe sandals and flip-flops', '/storage/categories/1748969521_Sandals.jpg', 'fas fa-tag', 22, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(53, 'Sneakers', 'product', NULL, 'Athletic and casual sneakers', '/storage/categories/1748969521_Sneakers.jpg', 'fas fa-tag', 22, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(54, 'Belts', 'product', NULL, 'Leather and fabric belts', '/storage/categories/1748969521_Belts.jpg', 'fas fa-tag', 23, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(55, 'Hats', 'product', NULL, 'Caps, hats and headwear', '/storage/categories/1748969521_Hats.jpg', 'fas fa-tag', 23, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(56, 'Scarves', 'product', NULL, 'Silk and cotton scarves', '/storage/categories/1748969521_Scarves.jpg', 'fas fa-tag', 23, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(57, 'Sunglasses', 'product', NULL, 'Designer and casual sunglasses', '/storage/categories/1748969521_Sunglasses.jpg', 'fas fa-tag', 23, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(58, 'Backpacks', 'product', NULL, 'School and travel backpacks', '/storage/categories/1748969521_Backpacks.jpg', 'fas fa-tag', 24, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(59, 'Crossbody bags', 'product', NULL, 'Small crossbody and shoulder bags', '/storage/categories/1748969521_Crossbody_bags.jpg', 'fas fa-tag', 24, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(60, 'Tote bags', 'product', NULL, 'Large tote and shopping bags', '/storage/categories/1748969521_Tote_bags.jpg', 'fas fa-tag', 24, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(61, 'Anklets', 'product', NULL, 'Ankle bracelets and chains', '/storage/categories/1748969521_Anklets.jpg', 'fas fa-tag', 25, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(62, 'Bracelets', 'product', NULL, 'Wrist bracelets and bangles', '/storage/categories/1748969521_Bracelets.jpg', 'fas fa-tag', 25, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(63, 'Earrings', 'product', NULL, 'Stud, hoop and drop earrings', '/storage/categories/1748969521_Earrings.jpg', 'fas fa-tag', 25, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(64, 'Necklaces', 'product', NULL, 'Chains, pendants and chokers', '/storage/categories/1748969521_Necklaces.jpg', 'fas fa-tag', 25, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(65, 'Rings', 'product', NULL, 'Fashion and statement rings', '/storage/categories/1748969521_Rings.jpg', 'fas fa-tag', 25, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(66, 'Blushes', 'product', NULL, 'Cheek color and bronzers', 'categories/0eyTpoOujRdw22PWKCpIi64ij9TBMxeAXskBI5vx.jpg', 'fas fa-tag', 26, 1, '2025-06-03 03:22:59', '2025-06-03 13:56:37', 0, 0, 0, NULL),
(67, 'Eyeshadows', 'product', NULL, 'Eye makeup and palettes', 'categories/MPvvbCTWvkVnFsUu2oGSA05Xz6tPrVxLr01HJTWo.jpg', 'fas fa-tag', 26, 1, '2025-06-03 03:22:59', '2025-06-03 13:58:26', 0, 0, 0, NULL),
(68, 'Foundations', 'product', NULL, 'Base makeup and concealers', 'categories/qTfgx88Pm5ZVisW6L2xWuwc2LZMr59H6fDYDXn3h.jpg', 'fas fa-tag', 26, 1, '2025-06-03 03:22:59', '2025-06-03 13:59:21', 0, 0, 0, NULL),
(69, 'Lipsticks', 'product', NULL, 'Lip color and glosses', 'categories/GAzEohTHW7rlsOXudT27iKjUPGOiMT46alipBEi4.jpg', 'fas fa-tag', 26, 1, '2025-06-03 03:22:59', '2025-06-04 01:05:32', 0, 0, 0, NULL),
(70, 'Mascaras', 'product', NULL, 'Eyelash makeup and primers', 'categories/SEBsQri4BqtdOaSzRWzIhDoGoqPXQ70niVttABSa.jpg', 'fas fa-tag', 26, 1, '2025-06-03 03:22:59', '2025-06-04 01:05:49', 0, 0, 0, NULL),
(71, 'Cleansers', 'product', NULL, 'Face wash and cleansing products', 'categories/PKHuI8ZuzevclFfi9jGGV8dGOxodFeBZr9xPsOSJ.jpg', 'fas fa-tag', 27, 1, '2025-06-03 03:22:59', '2025-06-03 13:57:13', 0, 0, 0, NULL),
(72, 'Face masks', 'product', NULL, 'Treatment and hydrating masks', 'categories/a5edSwypqkxeQmUOZzLFVlLISvI6ZPQbavdj14SM.jpg', 'fas fa-tag', 27, 1, '2025-06-03 03:22:59', '2025-06-03 13:59:02', 0, 0, 0, NULL),
(73, 'Moisturizers', 'product', NULL, 'Face and body moisturizers', 'categories/Pw2JZxeVsqTYGk9NUT7HtHoV78dW394gyRNKC9Ks.jpg', 'fas fa-tag', 27, 1, '2025-06-03 03:22:59', '2025-06-04 01:06:47', 0, 0, 0, NULL),
(74, 'Serums', 'product', NULL, 'Treatment serums and essences', 'categories/oShLGSvhqjYrGNt0kBw3GJBZ44X3NMCii5Ns7SWb.jpg', 'fas fa-tag', 27, 1, '2025-06-03 03:22:59', '2025-06-04 01:07:57', 0, 0, 0, NULL),
(75, 'Sunscreens', 'product', NULL, 'UV protection and SPF products', 'categories/32Zk5CefcGM2lPuCRb3ZhJHkiOrr7IdGIuT792vL.jpg', 'fas fa-tag', 27, 1, '2025-06-03 03:22:59', '2025-06-04 01:09:02', 0, 0, 0, NULL),
(76, 'Conditioners', 'product', NULL, 'Hair conditioners and treatments', 'categories/zHoje6XVEA924Ju8TZAbZ5CCqCus4W1h6R2c0vuq.jpg', 'fas fa-tag', 28, 1, '2025-06-03 03:22:59', '2025-06-03 13:57:48', 0, 0, 0, NULL),
(77, 'Hair oils', 'product', NULL, 'Nourishing hair oils and serums', 'categories/UVnlr0aeO0aJitHIY667XPbua9bPBBJhvg9s1zW6.jpg', 'fas fa-tag', 28, 1, '2025-06-03 03:22:59', '2025-06-04 01:04:53', 0, 0, 0, NULL),
(78, 'Shampoos', 'product', NULL, 'Cleansing shampoos for all hair types', 'categories/tNf1zqzERK4Dnpx8uUdXlqwSeImaj9LGuaJ8aEdI.jpg', 'fas fa-tag', 28, 1, '2025-06-03 03:22:59', '2025-06-04 01:08:10', 0, 0, 0, NULL),
(79, 'Clips', 'product', NULL, 'Hair clips and pins', '/storage/categories/1748969521_Clips.jpg', 'fas fa-tag', 29, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(80, 'Hairbands', 'product', NULL, 'Headbands and hair ties', 'categories/c19dLrH3mo0T0LsLtoROR0hK0gjdWsvakz0LvaDY.jpg', 'fas fa-tag', 29, 1, '2025-06-03 03:22:59', '2025-06-04 01:05:07', 0, 0, 0, NULL),
(81, 'Scrunchies', 'product', NULL, 'Fabric hair ties and scrunchies', 'categories/qugySGfm8OSygo8KMESFzCnk9uYlNB8R19krGrEE.jpg', 'fas fa-tag', 29, 1, '2025-06-03 03:22:59', '2025-06-04 01:07:32', 0, 0, 0, NULL),
(82, 'Body mists', 'product', NULL, 'Light body sprays and mists', 'categories/zmd8Yit5l4W3bbcAC4VU0pbftOxvzURz9RubwhyD.jpg', 'fas fa-tag', 30, 1, '2025-06-03 03:22:59', '2025-06-03 13:56:50', 0, 0, 0, NULL),
(83, 'Deodorants', 'product', NULL, 'Antiperspirants and deodorants', 'categories/3y5NgkeKHnC5tJ9rdwEElpSU6ryugvpEpPIZJcYs.jpg', 'fas fa-tag', 30, 1, '2025-06-03 03:22:59', '2025-06-03 13:58:05', 0, 0, 0, NULL),
(84, 'Perfumes', 'product', NULL, 'Eau de parfum and cologne', '/storage/categories/1748969521_Perfumes.jpg', 'fas fa-tag', 30, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(85, 'Bras', 'product', NULL, 'Support bras and bralettes', '/storage/categories/1748969521_Bras.jpg', 'fas fa-tag', 31, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(86, 'Lingerie', 'product', NULL, 'Intimate and sleepwear', '/storage/categories/1748969521_Lingerie.jpg', 'fas fa-tag', 31, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(87, 'Panties', 'product', NULL, 'Underwear and briefs', '/storage/categories/1748969521_Panties.jpg', 'fas fa-tag', 31, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(88, 'Shapewear', 'product', NULL, 'Body shaping undergarments', '/storage/categories/1748969521_Shapewear.jpg', 'fas fa-tag', 31, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(89, 'Belly support belts', 'product', NULL, 'Maternity support belts', 'categories/FAJqPrDJKIEfj4morcpZSsbFYFxHpFz2rwQ0V0D3.jpg', 'fas fa-tag', 32, 1, '2025-06-03 03:22:59', '2025-06-03 13:56:23', 0, 0, 0, NULL),
(90, 'Maternity clothing', 'product', NULL, 'Pregnancy-friendly clothing', 'categories/kv1l8CuD6smkUwsH2QRC2qILpooMVAtJGzvjlrbG.jpg', 'fas fa-tag', 32, 1, '2025-06-03 03:22:59', '2025-06-04 01:06:05', 0, 0, 0, NULL),
(91, 'Nursing bras', 'product', NULL, 'Breastfeeding support bras', 'categories/kdFzLJwDU2sAklU2SIbvk3HW7r73gvulGE324sW2.jpg', 'fas fa-tag', 32, 1, '2025-06-03 03:22:59', '2025-06-04 01:07:04', 0, 0, 0, NULL),
(92, 'Onesies', 'product', NULL, 'Baby bodysuits and onesies', '/storage/categories/1748969521_Onesies.jpg', 'fas fa-tag', 33, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(93, 'Outerwear', 'product', NULL, 'Baby jackets and coats', '/storage/categories/1748969521_Outerwear.jpg', 'fas fa-tag', 33, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(94, 'Sleepwear', 'product', NULL, 'Baby pajamas and sleep suits', '/storage/categories/1748969521_Sleepwear.jpg', 'fas fa-tag', 33, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(95, 'Baby carriers', 'product', NULL, 'Baby slings and carriers', '/storage/categories/1748969521_Baby_carriers.jpg', 'fas fa-tag', 34, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(96, 'Car seats', 'product', NULL, 'Infant and toddler car seats', '/storage/categories/1748969521_Car_seats.jpg', 'fas fa-tag', 34, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(97, 'Strollers', 'product', NULL, 'Baby strollers and pushchairs', '/storage/categories/1748969521_Strollers.jpg', 'fas fa-tag', 34, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(98, 'Bottles', 'product', NULL, 'Baby bottles and sippy cups', '/storage/categories/1748969521_Bottles.jpg', 'fas fa-tag', 35, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(99, 'Breast pumps', 'product', NULL, 'Electric and manual breast pumps', '/storage/categories/1748969521_Breast_pumps.jpg', 'fas fa-tag', 35, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(100, 'High chairs', 'product', NULL, 'Baby feeding chairs and boosters', '/storage/categories/1748969521_High_chairs.jpg', 'fas fa-tag', 35, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(101, 'Sterilizers', 'product', NULL, 'Bottle sterilizers and cleaners', '/storage/categories/1748969521_Sterilizers.jpg', 'fas fa-tag', 35, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(102, 'Analog', 'product', NULL, 'Traditional analog watches', '/storage/categories/1748969521_Analog.jpg', 'fas fa-tag', 36, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(103, 'Digital', 'product', NULL, 'Digital display watches', '/storage/categories/1748969521_Digital.jpg', 'fas fa-tag', 36, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(104, 'Smartwatches', 'product', NULL, 'Smart and fitness watches', '/storage/categories/1748969521_Smartwatches.jpg', 'fas fa-tag', 36, 1, '2025-06-03 03:22:59', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(106, 'Artistic Services', 'service', NULL, 'Creative and artistic services including photography, painting, and crafts', '/storage/categories/1748969521_Artistic_Services.jpg', 'fas fa-palette', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(107, 'Craft workshops', 'service', NULL, 'Creative craft workshops and classes', '/storage/categories/1748969521_Craft_workshops.jpg', 'fas fa-cut', 106, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(108, 'Painting classes', 'service', NULL, 'Art and painting classes', '/storage/categories/1748969521_Painting_classes.jpg', 'fas fa-paint-brush', 106, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(109, 'Photography sessions', 'service', NULL, 'Professional photography sessions', '/storage/categories/1748969521_Photography_sessions.jpg', 'fas fa-camera', 106, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(110, 'Pottery making', 'service', NULL, 'Pottery and ceramics classes', '/storage/categories/1748969521_Pottery_making.jpg', 'fas fa-hands', 106, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(111, 'Elderly Care & Companionship Services', 'service', NULL, 'Elderly care and companionship services for seniors', '/storage/categories/1748969521_Elderly_Care_&_Companionship_Services.jpg', 'fas fa-heart', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(112, 'Companionship visits', 'service', NULL, 'Companionship and social visits', '/storage/categories/1748969521_Companionship_visits.jpg', 'fas fa-handshake', 111, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(113, 'In-home care', 'service', NULL, 'In-home elderly care services', '/storage/categories/1748969521_In-home_care.jpg', 'fas fa-home', 111, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(114, 'Fitness Classes', 'service', NULL, 'Fitness classes and physical training sessions', '/storage/categories/1748969521_Fitness_Classes.jpg', 'fas fa-dumbbell', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(115, 'Pilates', 'service', NULL, 'Pilates classes and training', '/storage/categories/1748969521_Pilates.jpg', 'fas fa-dumbbell', 114, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(116, 'Strength training', 'service', NULL, 'Strength and resistance training', '/storage/categories/1748969521_Strength_training.jpg', 'fas fa-dumbbell', 114, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(117, 'Yoga', 'service', NULL, 'Yoga classes and sessions', '/storage/categories/1748969521_Yoga.jpg', 'fas fa-leaf', 114, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(118, 'Zumba', 'service', NULL, 'Zumba dance fitness classes', '/storage/categories/1748969521_Zumba.jpg', 'fas fa-music', 114, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(119, 'Women\'s Health', 'service', NULL, 'Women\'s health and femtech services', '/storage/categories/1748969522_Women\'s_Health.jpg', 'fas fa-heartbeat', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:49', 0, 0, 0, NULL),
(120, 'Fertility monitoring', 'service', NULL, 'Fertility tracking and monitoring services', '/storage/categories/1748969522_Fertility_monitoring.jpg', 'fas fa-heartbeat', 119, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(121, 'Menstrual tracking', 'service', NULL, 'Menstrual cycle tracking and health services', '/storage/categories/1748969522_Menstrual_tracking.jpg', 'fas fa-calendar-alt', 119, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(122, 'Mental Health Support', 'service', NULL, 'Mental health support and counseling', '/storage/categories/1748969522_Mental_Health_Support.jpg', 'fas fa-brain', 119, 1, '2025-06-03 05:26:41', '2025-06-03 09:39:40', 0, 0, 0, NULL),
(123, 'Pregnancy guides', 'service', NULL, 'Pregnancy guidance and support services', '/storage/categories/1748969522_Pregnancy_guides.jpg', 'fas fa-baby', 119, 1, '2025-06-03 05:26:41', '2025-06-03 09:42:28', 0, 0, 0, NULL),
(125, 'Makeup Services', 'service', NULL, 'Professional makeup and beauty services', '/storage/categories/1748969522_Makeup_Services.jpg', 'fas fa-paint-brush', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(126, 'Bridal makeup', 'service', NULL, 'Bridal makeup and styling services', '/storage/categories/1748969522_Bridal_makeup.jpg', 'fas fa-ring', 125, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(127, 'Event makeup', 'service', NULL, 'Special event makeup services', '/storage/categories/1748969522_Event_makeup.jpg', 'fas fa-star', 125, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(128, 'Tutorials', 'service', NULL, 'Makeup tutorials and training', '/storage/categories/1748969522_Tutorials.jpg', 'fas fa-play-circle', 125, 1, '2025-06-03 05:26:41', '2025-06-03 09:44:12', 0, 0, 0, NULL),
(129, 'Nail Care', 'service', NULL, 'Professional nail care and beauty services', '/storage/categories/1748969522_Nail_Care.jpg', 'fas fa-hand-sparkles', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 09:40:17', 0, 0, 0, NULL),
(130, 'Manicures', 'service', NULL, 'Professional manicure services', '/storage/categories/1748969522_Manicures.jpg', 'fas fa-hand-paper', 129, 1, '2025-06-03 05:26:41', '2025-06-03 09:38:44', 0, 0, 0, NULL),
(131, 'Nail art', 'service', NULL, 'Creative nail art and design services', '/storage/categories/1748969522_Nail_art.jpg', 'fas fa-paint-brush', 129, 1, '2025-06-03 05:26:41', '2025-06-03 09:40:08', 0, 0, 0, NULL),
(132, 'Pedicures', 'service', NULL, 'Professional pedicure services', '/storage/categories/1748969522_Pedicures.jpg', 'fas fa-shoe-prints', 129, 1, '2025-06-03 05:26:41', '2025-06-03 09:41:05', 0, 0, 0, NULL),
(133, 'Nutrition Counseling', 'service', NULL, 'Nutrition counseling and dietary guidance services', '/storage/categories/1748969522_Nutrition_Counseling.jpg', 'fas fa-apple-alt', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(134, 'Diet plans', 'service', NULL, 'Personalized diet planning services', '/storage/categories/1748969522_Diet_plans.jpg', 'fas fa-clipboard-list', 133, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(135, 'Weight management programs', 'service', NULL, 'Weight management and fitness programs', '/storage/categories/1748969522_Weight_management_programs.jpg', 'fas fa-weight', 133, 1, '2025-06-03 05:26:41', '2025-06-03 09:44:27', 0, 0, 0, NULL),
(136, 'Salon Services', 'service', NULL, 'Professional hair and beauty salon services', '/storage/categories/1748969522_Salon_Services.jpg', 'fas fa-cut', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 09:42:46', 0, 0, 0, NULL),
(137, 'Coloring', 'service', NULL, 'Hair coloring and highlighting services', '/storage/categories/1748969522_Coloring.jpg', 'fas fa-palette', 136, 1, '2025-06-03 05:26:41', '2025-06-03 09:33:35', 0, 0, 0, NULL),
(138, 'Haircuts', 'service', NULL, 'Professional hair cutting services', '/storage/categories/1748969522_Haircuts.jpg', 'fas fa-cut', 136, 1, '2025-06-03 05:26:41', '2025-06-03 09:36:49', 0, 0, 0, NULL),
(139, 'Styling', 'service', NULL, 'Hair styling and design services', '/storage/categories/1748969522_Styling.jpg', 'fas fa-magic', 136, 1, '2025-06-03 05:26:41', '2025-06-03 09:43:46', 0, 0, 0, NULL),
(140, 'Spa Treatments', 'service', NULL, 'Relaxing spa treatments and wellness therapies', '/storage/categories/1748969522_Spa_Treatments.jpg', 'fas fa-spa', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 09:43:05', 0, 0, 0, NULL),
(141, 'Body scrubs', 'service', NULL, 'Exfoliating body scrub treatments', '/storage/categories/1748969522_Body_scrubs.jpg', 'fas fa-shower', 140, 1, '2025-06-03 05:26:41', '2025-06-03 09:32:45', 0, 0, 0, NULL),
(142, 'Facials', 'service', NULL, 'Professional facial treatments', '/storage/categories/1748969522_Facials.jpg', 'fas fa-smile', 140, 1, '2025-06-03 05:26:41', '2025-06-03 09:35:30', 0, 0, 0, NULL),
(143, 'Massages', 'service', NULL, 'Therapeutic and relaxation massages', '/storage/categories/1748969522_Massages.jpg', 'fas fa-hands', 140, 1, '2025-06-03 05:26:41', '2025-06-03 09:39:05', 0, 0, 0, NULL),
(144, 'Therapy Sessions', 'service', NULL, 'Professional therapy and counseling sessions', '/storage/categories/1748969522_Therapy_Sessions.jpg', 'fas fa-hands-helping', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(145, 'Couple Therapy', 'service', NULL, 'Professional Couple Therapy  services', '/storage/categories/1748969522_Couple_Therapy.jpg', 'fas fa-concierge-bell', 144, 1, '2025-06-03 05:26:41', '2025-06-03 09:34:16', 0, 0, 0, NULL),
(146, 'Family therapy', 'service', NULL, 'Family therapy and counseling services', '/storage/categories/1748969522_Family_therapy.jpg', 'fas fa-users', 144, 1, '2025-06-03 05:26:41', '2025-06-03 09:35:42', 0, 0, 0, NULL),
(147, 'Individual Therapy', 'service', NULL, 'Individual therapy and counseling sessions', '/storage/categories/1748969522_Individual_Therapy.jpg', 'fas fa-user', 144, 1, '2025-06-03 05:26:41', '2025-06-03 13:24:50', 0, 0, 0, NULL),
(148, 'Wellness Workshops', 'service', NULL, 'Health and wellness workshops and classes', '/storage/categories/1748969522_Wellness_Workshops.jpg', 'fas fa-leaf', NULL, 1, '2025-06-03 05:26:41', '2025-06-03 09:44:39', 0, 0, 0, NULL),
(149, 'Healthy cooking', 'service', NULL, 'Healthy cooking classes and workshops', '/storage/categories/1748969522_Healthy_cooking.jpg', 'fas fa-utensils', 148, 1, '2025-06-03 05:26:41', '2025-06-03 09:37:34', 0, 0, 0, NULL),
(150, 'Mindfulness', 'service', NULL, 'Mindfulness and meditation sessions', '/storage/categories/1748969522_Mindfulness.jpg', 'fas fa-brain', 148, 1, '2025-06-03 05:26:41', '2025-06-03 09:39:54', 0, 0, 0, NULL),
(151, 'Stress management', 'service', NULL, 'Stress management workshops and techniques', '/storage/categories/1748969522_Stress_management.jpg', 'fas fa-leaf', 148, 1, '2025-06-03 05:26:41', '2025-06-03 09:43:32', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `business_type` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `average_rating` double NOT NULL DEFAULT 0,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `vendor_score` int(11) NOT NULL DEFAULT 0,
  `last_score_calculation` timestamp NULL DEFAULT NULL,
  `can_deliver` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether vendor can handle own deliveries'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `name`, `business_type`, `registration_number`, `description`, `logo`, `website`, `email`, `phone`, `address`, `city`, `state`, `zip_code`, `country`, `tax_id`, `status`, `created_at`, `updated_at`, `view_count`, `order_count`, `average_rating`, `rating_count`, `vendor_score`, `last_score_calculation`, `can_deliver`) VALUES
(1, 2, 'Tech Solutions Inc.', 'Technology', 'REG-TS-2023', 'Leading provider of technology solutions for businesses of all sizes.', '/images/placeholder.jpg', 'https://techsolutions.example.com', 'info@techsolutions.example.com', '1234567890', '123 Tech Street', 'San Francisco', 'CA', '94105', 'USA', 'TS-12345', 'active', '2025-06-03 03:19:22', '2025-06-03 03:19:22', 0, 0, 0, 0, 0, NULL, 1),
(2, 3, 'Wellness Center', 'Health & Wellness', 'REG-WC-2023', 'Comprehensive wellness services for mind and body.', '/images/placeholder.jpg', 'https://wellness.example.com', 'info@wellness.example.com', '2345678901', '456 Health Avenue', 'Los Angeles', 'CA', '90001', 'USA', 'WC-67890', 'active', '2025-06-03 03:19:22', '2025-06-03 03:19:22', 0, 0, 0, 0, 0, NULL, 1),
(3, 4, 'Gourmet Delights', 'Food & Beverage', 'REG-GD-2023', 'Premium food products and catering services.', '/images/placeholder.jpg', 'https://gourmet.example.com', 'info@gourmet.example.com', '3456789012', '789 Culinary Blvd', 'New York', 'NY', '10001', 'USA', 'GD-24680', 'active', '2025-06-03 03:19:22', '2025-06-03 03:19:22', 0, 0, 0, 0, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE `deals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `promotional_message` varchar(50) DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `applies_to` enum('all','products','categories') NOT NULL DEFAULT 'all',
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_ids`)),
  `category_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`category_ids`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deals`
--

INSERT INTO `deals` (`id`, `user_id`, `title`, `description`, `promotional_message`, `discount_percentage`, `start_date`, `end_date`, `image`, `status`, `applies_to`, `product_ids`, `category_ids`, `created_at`, `updated_at`) VALUES
(9, 2, 'John Deal', 'Good deals form John', 'Mega Sale', 20.00, '2025-05-29', '2025-06-28', 'deals/ue4d4uMTYaIVdPdx9dbwWOI0cDMBjWS28cES5f1l.jpg', 'active', 'categories', '[29]', '[\"29\",\"30\"]', '2025-05-29 16:50:10', '2025-05-29 16:50:10'),
(10, 2, 'Special Electronics Sale', 'Get amazing discounts on all Electronics products!', 'Limited Time!', 20.00, '2025-05-29', '2025-07-03', NULL, 'active', 'categories', NULL, '[1]', '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(11, 2, 'Flash Sale: Premium Smartphones', 'Exclusive discount on Premium Smartphones', 'Flash Sale!', 15.00, '2025-05-31', '2025-06-18', NULL, 'active', 'products', '[1]', NULL, '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(12, 2, 'Store-wide Mega Sale', 'Incredible discounts on all our products!', 'Mega Sale!', 25.00, '2025-05-27', '2025-07-18', NULL, 'active', 'all', NULL, NULL, '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(13, 3, 'Special Home & Kitchen Sale', 'Get amazing discounts on all Home & Kitchen products!', 'Limited Time!', 25.00, '2025-05-29', '2025-07-03', NULL, 'active', 'categories', NULL, '[2]', '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(14, 3, 'Flash Sale: Premium Laptops', 'Exclusive discount on Premium Laptops', 'Flash Sale!', 18.00, '2025-05-31', '2025-06-18', NULL, 'active', 'products', '[3]', NULL, '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(15, 4, 'Special Food & Beverages Sale', 'Get amazing discounts on all Food & Beverages products!', 'Limited Time!', 30.00, '2025-05-29', '2025-07-03', NULL, 'active', 'categories', NULL, '[3]', '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(16, 4, 'Flash Sale: Premium Audio', 'Exclusive discount on Premium Audio', 'Flash Sale!', 21.00, '2025-05-31', '2025-06-18', NULL, 'active', 'products', '[5]', NULL, '2025-06-03 01:49:46', '2025-06-03 01:49:46'),
(17, 2, 'Special Electronics Sale', 'Get amazing discounts on all Electronics products!', 'Limited Time!', 20.00, '2025-05-29', '2025-07-03', NULL, 'active', 'categories', NULL, '[1]', '2025-06-03 03:19:47', '2025-06-03 03:19:47'),
(18, 2, 'Flash Sale: Premium Smartphones', 'Exclusive discount on Premium Smartphones', 'Flash Sale!', 15.00, '2025-05-31', '2025-06-18', NULL, 'active', 'products', '[1]', NULL, '2025-06-03 03:19:47', '2025-06-03 03:19:47'),
(19, 2, 'Store-wide Mega Sale', 'Incredible discounts on all our products!', 'Mega Sale!', 25.00, '2025-05-27', '2025-07-18', NULL, 'active', 'all', NULL, NULL, '2025-06-03 03:19:47', '2025-06-03 03:19:47'),
(20, 3, 'Special Home & Kitchen Sale', 'Get amazing discounts on all Home & Kitchen products!', 'Limited Time!', 25.00, '2025-05-29', '2025-07-03', NULL, 'active', 'categories', NULL, '[2]', '2025-06-03 03:19:47', '2025-06-03 03:19:47'),
(21, 3, 'Flash Sale: Premium Laptops', 'Exclusive discount on Premium Laptops', 'Flash Sale!', 18.00, '2025-05-31', '2025-06-18', NULL, 'active', 'products', '[3]', NULL, '2025-06-03 03:19:47', '2025-06-03 03:19:47'),
(22, 4, 'Special Food & Beverages Sale', 'Get amazing discounts on all Food & Beverages products!', 'Limited Time!', 30.00, '2025-05-29', '2025-07-03', NULL, 'active', 'categories', NULL, '[3]', '2025-06-03 03:19:47', '2025-06-03 03:19:47'),
(23, 4, 'Flash Sale: Premium Audio', 'Exclusive discount on Premium Audio', 'Flash Sale!', 21.00, '2025-05-31', '2025-06-18', NULL, 'active', 'products', '[5]', NULL, '2025-06-03 03:19:47', '2025-06-03 03:19:47');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gift_options`
--

CREATE TABLE `gift_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `is_gift` tinyint(1) NOT NULL DEFAULT 0,
  `gift_wrap` tinyint(1) NOT NULL DEFAULT 0,
  `gift_wrap_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gift_wrap_type` varchar(255) DEFAULT NULL,
  `gift_message` text DEFAULT NULL,
  `gift_from` varchar(255) DEFAULT NULL,
  `gift_to` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2023_07_01_000001_create_categories_table', 1),
(5, '2023_07_01_000002_create_companies_table', 1),
(6, '2023_07_01_000003_create_branches_table', 1),
(7, '2023_07_01_000004_create_products_table', 1),
(8, '2023_07_01_000005_create_reviews_table', 1),
(9, '2023_07_01_000006_create_services_table', 1),
(10, '2023_07_01_000007_add_role_to_users_table', 1),
(11, '2023_07_02_000001_add_type_and_icon_to_categories_table', 1),
(12, '2023_07_02_000001_create_provider_products_table', 1),
(13, '2023_07_02_000002_add_business_fields_to_companies_table', 1),
(14, '2023_07_10_000001_add_columns_to_orders_table', 1),
(15, '2023_07_10_000002_add_columns_to_order_items_table', 1),
(16, '2023_07_10_000003_add_columns_to_bookings_table', 1),
(17, '2023_07_15_000001_add_status_to_provider_products_table', 1),
(18, '2023_08_01_000000_create_personal_access_tokens_table', 1),
(19, '2023_08_01_000001_drop_product_specifications_table', 1),
(20, '2023_08_15_add_estimated_delivery_to_orders_table', 1),
(21, '2023_08_20_000001_create_vendor_order_statuses_table', 1),
(22, '2023_11_15_000001_add_discount_columns_to_order_items_table', 1),
(23, '2023_11_16_000001_add_discount_column_to_orders_table', 1),
(24, '2023_11_16_000002_create_order_item_options_table', 1),
(25, '2023_11_16_000003_create_gift_options_table', 1),
(26, '2023_11_16_000004_create_product_option_tables', 1),
(27, '2024_07_01_000001_create_provider_locations_table', 1),
(28, '2024_07_01_000002_create_user_locations_table', 1),
(29, '2024_07_15_000001_create_provider_locations_table_fix', 1),
(30, '2025_05_01_160244_create_transactions_table', 1),
(31, '2025_05_01_160304_create_chats_table', 1),
(32, '2025_05_01_160315_create_chat_messages_table', 1),
(33, '2025_05_01_160326_create_wishlists_table', 1),
(34, '2025_05_02_170737_create_payment_methods_table', 1),
(35, '2025_05_02_170743_create_payout_methods_table', 1),
(36, '2025_05_02_170749_create_payout_preferences_table', 1),
(37, '2025_05_02_170905_create_payment_transactions_table', 1),
(38, '2025_05_02_191922_create_payment_methods_table', 1),
(39, '2025_05_02_191928_create_payout_methods_table', 1),
(40, '2025_05_02_191934_create_payout_preferences_table', 1),
(41, '2025_05_02_191940_create_payment_transactions_table', 1),
(42, '2025_05_06_131923_add_featured_flag_to_products_table', 1),
(43, '2025_05_06_131937_add_featured_flag_to_branches_table', 1),
(44, '2025_05_06_135909_add_featured_flag_to_services_table', 1),
(45, '2025_05_10_234601_create_deals_table', 1),
(46, '2025_05_13_214254_create_providers_table', 1),
(47, '2025_05_13_230615_add_business_name_to_provider_profiles_table', 1),
(48, '2025_05_13_231115_add_status_to_provider_profiles_table', 1),
(49, '2025_05_13_231242_fix_provider_profiles_table_columns', 1),
(50, '2025_05_13_231902_update_provider_profiles_company_name_nullable', 1),
(51, '2025_05_16_181207_add_user_id_to_provider_profiles_table', 1),
(52, '2025_05_16_182320_add_sku_to_products_table', 1),
(53, '2025_05_20_000001_add_promotional_message_to_deals_table', 1),
(54, '2025_05_21_012520_add_tracking_fields_to_categories_table', 1),
(55, '2025_05_21_012528_add_tracking_fields_to_companies_table', 1),
(56, '2025_05_21_012536_add_tracking_fields_to_branches_table', 1),
(57, '2025_05_21_210741_add_branch_image_to_branches_table', 1),
(58, '2025_05_22_203935_update_provider_products_is_active', 2),
(59, '2025_05_22_230230_add_is_active_to_provider_products_table', 3),
(60, '2025_05_22_230309_add_branch_id_to_provider_products_table', 3),
(61, '2025_05_22_230346_make_product_id_nullable_in_provider_products_table', 4),
(62, '2025_05_22_230618_add_category_id_to_provider_products_table', 4),
(63, '2025_05_22_230658_add_sku_to_provider_products_table', 4),
(64, '2025_05_22_230740_add_stock_to_provider_products_table', 4),
(65, '2025_05_22_231025_add_original_price_to_provider_products_table', 4),
(66, '2025_05_22_231111_add_price_to_provider_products_table', 4),
(67, '2025_05_22_231150_add_description_to_provider_products_table', 4),
(68, '2025_05_22_231232_add_product_name_to_provider_products_table', 4),
(69, '2025_05_22_231314_add_image_to_provider_products_table', 4),
(70, '2025_06_01_000001_update_payment_methods_table', 4),
(71, '2025_06_01_000002_update_payout_methods_table', 4),
(72, '2025_06_01_000003_update_payment_transactions_table', 4),
(73, '2025_06_01_000004_add_soft_deletes_to_payment_methods', 4),
(74, '2025_06_01_000005_add_soft_deletes_to_payout_methods', 4),
(75, '2025_06_01_000006_add_soft_deletes_to_payment_transactions', 4),
(76, '2025_06_10_000001_add_can_deliver_to_companies_table', 4),
(77, '2025_06_10_000002_add_shipping_fields_to_orders_table', 4),
(78, '2025_06_10_000003_create_shipments_table', 4),
(79, '2025_06_10_000004_add_vendor_id_to_order_items_table', 4),
(80, '2025_07_01_000001_add_user_id_to_products_table', 4),
(81, '2025_07_15_000001_create_product_specifications_table', 4),
(82, '2025_07_15_000002_create_service_specifications_table', 4),
(83, '2025_07_15_000003_create_product_branches_table', 4),
(84, '2025_08_01_000001_create_product_specification_tables', 4),
(85, '2025_08_01_000001_create_product_specifications_tables', 4),
(86, '2025_08_15_000001_add_home_service_to_services_table', 4),
(87, '2025_08_15_000001_add_item_status_and_specifications_to_order_items', 4),
(88, '2025_10_01_000001_add_sku_to_products_table', 4),
(89, '2025_10_01_000001_add_user_id_to_provider_profiles_table', 4),
(90, '2025_10_01_000002_fix_provider_profiles_table_structure', 4),
(91, '2025_10_01_000003_make_branch_id_nullable_in_products_table', 4),
(92, '2025_10_01_000004_add_missing_columns_to_provider_products_table', 4),
(93, '2025_12_01_000001_create_vendor_ratings_table', 5),
(94, '2025_12_01_000002_create_branch_ratings_table', 6),
(95, '2025_12_01_000003_create_provider_ratings_table', 7),
(96, '2025_12_01_000004_add_rating_fields_to_users_table', 8),
(97, '2025_12_01_000005_add_rating_fields_to_branches_table', 9),
(98, '2025_12_01_000006_add_rating_fields_to_providers_table', 10),
(99, '2025_01_15_000001_create_view_tracking_table', 11),
(100, '2025_01_15_000002_add_view_count_to_providers_table', 12),
(101, '2025_10_02_000001_make_product_id_nullable_in_provider_products_table', 13),
(102, '2025_10_02_000001_make_provider_id_nullable_in_provider_profiles_table', 14),
(103, '2025_10_15_000001_create_product_specifications_table', 14),
(104, '2025_01_15_000001_add_additional_info_to_product_sizes', 15),
(105, '2025_01_16_000001_create_size_categories_table', 16),
(106, '2025_01_16_000002_create_standardized_sizes_table', 16),
(107, '2025_01_16_000003_add_size_category_to_product_sizes_table', 16),
(108, '2025_01_16_000004_add_category_size_mapping_to_categories_table', 16),
(109, '2025_01_16_000005_run_size_category_setup', 16),
(110, '2025_01_20_000001_create_product_color_sizes_table', 17),
(111, '2025_12_01_000006_add_emirate_to_branches_table', 18);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total discount amount applied to the order',
  `status` enum('pending','processing','partially_shipped','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estimated_delivery` timestamp NULL DEFAULT NULL,
  `shipping_method` varchar(255) NOT NULL DEFAULT 'vendor' COMMENT 'Assigned shipping method: ''vendor'' or ''aramex''',
  `shipping_status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT 'Delivery status (e.g. pending, shipped, delivered)',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Cost of shipping',
  `tracking_number` varchar(255) DEFAULT NULL COMMENT 'Tracking number for the shipment',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(255) DEFAULT NULL,
  `shipping_country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `branch_id`, `order_number`, `total`, `discount`, `status`, `payment_status`, `payment_method`, `shipping_address`, `billing_address`, `notes`, `created_at`, `updated_at`, `estimated_delivery`, `shipping_method`, `shipping_status`, `shipping_cost`, `tracking_number`, `customer_name`, `customer_phone`, `shipping_city`, `shipping_country`) VALUES
(1, 5, 5, 'ORD-35A857-20250603', 134.39, 0.00, 'delivered', 'paid', 'cash_on_delivery', '{\"name\":\"Alice Customer\",\"address_line1\":\"620 Oak Avenue\",\"city\":\"New York\",\"state\":\"NY\",\"postal_code\":73969,\"country\":\"USA\",\"phone\":\"5678901234\"}', '{\"name\":\"Alice Customer\",\"address_line1\":\"620 Oak Avenue\",\"city\":\"New York\",\"state\":\"NY\",\"postal_code\":73969,\"country\":\"USA\",\"phone\":\"5678901234\"}', 'The doorbell is broken, please knock.', '2025-03-25 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(2, 5, 5, 'ORD-363770-20250603', 337.80, 0.00, 'cancelled', 'refunded', 'cash_on_delivery', '{\"name\":\"Alice Customer\",\"address_line1\":\"508 River Road\",\"city\":\"Columbus\",\"state\":\"NE\",\"postal_code\":33991,\"country\":\"USA\",\"phone\":\"5678901234\"}', '{\"name\":\"Alice Customer\",\"address_line1\":\"508 River Road\",\"city\":\"Columbus\",\"state\":\"NE\",\"postal_code\":33991,\"country\":\"USA\",\"phone\":\"5678901234\"}', 'Please deliver after 5 PM.', '2025-05-12 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(3, 5, 5, 'ORD-368303-20250603', 422.25, 0.00, 'cancelled', 'refunded', 'cash_on_delivery', '{\"name\":\"Alice Customer\",\"address_line1\":\"541 Main Street\",\"city\":\"Phoenix\",\"state\":\"NE\",\"postal_code\":93616,\"country\":\"USA\",\"phone\":\"5678901234\"}', '{\"name\":\"Alice Customer\",\"address_line1\":\"541 Main Street\",\"city\":\"Phoenix\",\"state\":\"NE\",\"postal_code\":93616,\"country\":\"USA\",\"phone\":\"5678901234\"}', '', '2025-05-06 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(4, 6, 2, 'ORD-36C030-20250603', 41.44, 0.00, 'cancelled', 'refunded', 'cash_on_delivery', '{\"name\":\"Bob Customer\",\"address_line1\":\"314 Main Street\",\"city\":\"Houston\",\"state\":\"NM\",\"postal_code\":43934,\"country\":\"USA\",\"phone\":\"6789012345\"}', '{\"name\":\"Bob Customer\",\"address_line1\":\"314 Main Street\",\"city\":\"Houston\",\"state\":\"NM\",\"postal_code\":43934,\"country\":\"USA\",\"phone\":\"6789012345\"}', '', '2025-05-20 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(5, 6, 4, 'ORD-36F4B3-20250603', 624.52, 0.00, 'pending', 'paid', 'paypal', '{\"name\":\"Bob Customer\",\"address_line1\":\"816 River Road\",\"city\":\"San Diego\",\"state\":\"VA\",\"postal_code\":92189,\"country\":\"USA\",\"phone\":\"6789012345\"}', '{\"name\":\"Bob Customer\",\"address_line1\":\"816 River Road\",\"city\":\"San Diego\",\"state\":\"VA\",\"postal_code\":92189,\"country\":\"USA\",\"phone\":\"6789012345\"}', 'Please deliver to the back entrance.', '2025-03-14 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(6, 6, 2, 'ORD-376A23-20250603', 120.22, 0.00, 'delivered', 'paid', 'cash_on_delivery', '{\"name\":\"Bob Customer\",\"address_line1\":\"582 Pine Road\",\"city\":\"Austin\",\"state\":\"KS\",\"postal_code\":10500,\"country\":\"USA\",\"phone\":\"6789012345\"}', '{\"name\":\"Bob Customer\",\"address_line1\":\"582 Pine Road\",\"city\":\"Austin\",\"state\":\"KS\",\"postal_code\":10500,\"country\":\"USA\",\"phone\":\"6789012345\"}', 'Call before delivery.', '2025-05-23 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(7, 7, 2, 'ORD-379735-20250603', 539.56, 0.00, 'processing', 'refunded', 'credit_card', '{\"name\":\"Charlie Customer\",\"address_line1\":\"999 Main Street\",\"city\":\"New York\",\"state\":\"LA\",\"postal_code\":12033,\"country\":\"USA\",\"phone\":\"7890123456\"}', '{\"name\":\"Charlie Customer\",\"address_line1\":\"999 Main Street\",\"city\":\"New York\",\"state\":\"LA\",\"postal_code\":12033,\"country\":\"USA\",\"phone\":\"7890123456\"}', 'Fragile items, handle with care.', '2025-05-16 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(8, 7, 4, 'ORD-37E5E6-20250603', 568.86, 0.00, 'cancelled', 'refunded', 'credit_card', '{\"name\":\"Charlie Customer\",\"address_line1\":\"310 River Road\",\"city\":\"Philadelphia\",\"state\":\"NE\",\"postal_code\":63856,\"country\":\"USA\",\"phone\":\"7890123456\"}', '{\"name\":\"Charlie Customer\",\"address_line1\":\"310 River Road\",\"city\":\"Philadelphia\",\"state\":\"NE\",\"postal_code\":63856,\"country\":\"USA\",\"phone\":\"7890123456\"}', '', '2025-05-27 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(9, 7, 3, 'ORD-383C8E-20250603', 211.00, 0.00, 'pending', 'pending', 'cash_on_delivery', '{\"name\":\"Charlie Customer\",\"address_line1\":\"830 River Road\",\"city\":\"Austin\",\"state\":\"PA\",\"postal_code\":38789,\"country\":\"USA\",\"phone\":\"7890123456\"}', '{\"name\":\"Charlie Customer\",\"address_line1\":\"830 River Road\",\"city\":\"Austin\",\"state\":\"PA\",\"postal_code\":38789,\"country\":\"USA\",\"phone\":\"7890123456\"}', '', '2025-05-04 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(10, 7, 4, 'ORD-387B65-20250603', 429.81, 0.00, 'pending', 'paid', 'paypal', '{\"name\":\"Charlie Customer\",\"address_line1\":\"410 Willow Lane\",\"city\":\"Houston\",\"state\":\"WV\",\"postal_code\":38790,\"country\":\"USA\",\"phone\":\"7890123456\"}', '{\"name\":\"Charlie Customer\",\"address_line1\":\"410 Willow Lane\",\"city\":\"Houston\",\"state\":\"WV\",\"postal_code\":38790,\"country\":\"USA\",\"phone\":\"7890123456\"}', '', '2025-03-05 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(11, 8, 1, 'ORD-38DFFC-20250603', 195.88, 0.00, 'pending', 'pending', 'paypal', '{\"name\":\"Diana Customer\",\"address_line1\":\"206 Elm Street\",\"city\":\"San Antonio\",\"state\":\"WV\",\"postal_code\":20952,\"country\":\"USA\",\"phone\":\"8901234567\"}', '{\"name\":\"Diana Customer\",\"address_line1\":\"206 Elm Street\",\"city\":\"San Antonio\",\"state\":\"WV\",\"postal_code\":20952,\"country\":\"USA\",\"phone\":\"8901234567\"}', 'Please deliver to the back entrance.', '2025-05-08 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL),
(12, 8, 5, 'ORD-391876-20250603', 84.45, 0.00, 'cancelled', 'refunded', 'paypal', '{\"name\":\"Diana Customer\",\"address_line1\":\"591 Maple Drive\",\"city\":\"Chicago\",\"state\":\"OH\",\"postal_code\":25763,\"country\":\"USA\",\"phone\":\"8901234567\"}', '{\"name\":\"Diana Customer\",\"address_line1\":\"591 Maple Drive\",\"city\":\"Chicago\",\"state\":\"OH\",\"postal_code\":25763,\"country\":\"USA\",\"phone\":\"8901234567\"}', 'Fragile items, handle with care.', '2025-03-15 03:19:47', '2025-06-03 03:19:47', NULL, 'vendor', 'pending', 0.00, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'The vendor (company) responsible for this item',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `original_price` decimal(10,2) DEFAULT NULL COMMENT 'Original price before any discounts',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount percentage applied to this item',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount amount in currency',
  `applied_deal_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID of the deal that was applied to this item',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Status of this specific order item',
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Product specifications at time of order' CHECK (json_valid(`specifications`)),
  `color_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Selected product color ID',
  `color_name` varchar(255) DEFAULT NULL COMMENT 'Selected product color name',
  `color_value` varchar(255) DEFAULT NULL COMMENT 'Selected product color value/code',
  `color_image` varchar(255) DEFAULT NULL COMMENT 'Selected product color image',
  `size_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Selected product size ID',
  `size_name` varchar(255) DEFAULT NULL COMMENT 'Selected product size name',
  `size_value` varchar(255) DEFAULT NULL COMMENT 'Selected product size value',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_options`
--

CREATE TABLE `order_item_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `option_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `option_value_id` bigint(20) UNSIGNED DEFAULT NULL,
  `option_name` varchar(255) NOT NULL,
  `option_value` varchar(255) NOT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `custom_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_status_history`
--

CREATE TABLE `order_item_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `provider_type` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `card_brand` varchar(255) DEFAULT NULL,
  `last_four` varchar(255) DEFAULT NULL,
  `expiry_month` varchar(255) DEFAULT NULL,
  `expiry_year` varchar(255) DEFAULT NULL,
  `billing_email` varchar(255) DEFAULT NULL,
  `billing_address_line1` varchar(255) DEFAULT NULL,
  `billing_address_line2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(255) DEFAULT NULL,
  `billing_state` varchar(255) DEFAULT NULL,
  `billing_postal_code` varchar(255) DEFAULT NULL,
  `billing_country` varchar(255) DEFAULT NULL,
  `token_id` varchar(255) DEFAULT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `user_id`, `provider_type`, `payment_type`, `name`, `card_brand`, `last_four`, `expiry_month`, `expiry_year`, `billing_email`, `billing_address_line1`, `billing_address_line2`, `billing_city`, `billing_state`, `billing_postal_code`, `billing_country`, `token_id`, `customer_id`, `is_default`, `is_verified`, `verified_at`, `meta_data`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'stripe', 'credit_card', 'Visa Card', 'visa', '1407', '2', '2028', 'admin@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(2, 1, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '3104', '10', '2028', 'admin@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(3, 1, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'admin@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(4, 2, 'stripe', 'credit_card', 'Visa Card', 'visa', '8593', '2', '2030', 'john@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(5, 2, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '5381', '1', '2025', 'john@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(6, 2, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'john@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(7, 3, 'stripe', 'credit_card', 'Visa Card', 'visa', '8017', '7', '2028', 'jane@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(8, 3, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '7588', '8', '2024', 'jane@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(9, 3, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'jane@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(10, 4, 'stripe', 'credit_card', 'Visa Card', 'visa', '4329', '7', '2028', 'mike@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(11, 4, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '1390', '10', '2028', 'mike@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(12, 4, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'mike@vendor.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(13, 5, 'stripe', 'credit_card', 'Visa Card', 'visa', '6142', '10', '2026', 'alice@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(14, 5, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '4008', '7', '2030', 'alice@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(15, 5, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'alice@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(16, 6, 'stripe', 'credit_card', 'Visa Card', 'visa', '4979', '8', '2026', 'bob@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(17, 6, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '3972', '7', '2025', 'bob@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(18, 6, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'bob@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(19, 7, 'stripe', 'credit_card', 'Visa Card', 'visa', '6691', '9', '2024', 'charlie@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(20, 7, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '6258', '9', '2028', 'charlie@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(21, 7, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'charlie@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(22, 8, 'stripe', 'credit_card', 'Visa Card', 'visa', '5957', '7', '2027', 'diana@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(23, 8, 'stripe', 'credit_card', 'Mastercard', 'mastercard', '8096', '1', '2027', 'diana@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"card_type\\\":\\\"credit\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL),
(24, 8, 'paypal', 'paypal', 'PayPal Account', NULL, NULL, NULL, NULL, 'diana@customer.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '\"{\\\"account_type\\\":\\\"personal\\\"}\"', '2025-06-03 03:19:47', '2025-06-03 03:19:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `provider_transaction_id` varchar(255) DEFAULT NULL,
  `provider_status` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `currency` varchar(255) NOT NULL DEFAULT 'USD',
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payout_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `related_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `notes` text DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payout_methods`
--

CREATE TABLE `payout_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `provider_type` varchar(255) NOT NULL,
  `payout_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `last_four` varchar(255) DEFAULT NULL,
  `routing_number` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `account_holder_name` varchar(255) DEFAULT NULL,
  `account_holder_type` varchar(255) DEFAULT NULL,
  `currency` varchar(255) NOT NULL DEFAULT 'USD',
  `country` varchar(255) DEFAULT NULL,
  `payout_email` varchar(255) DEFAULT NULL,
  `token_id` varchar(255) DEFAULT NULL,
  `external_account_id` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payout_preferences`
--

CREATE TABLE `payout_preferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payout_frequency` varchar(255) NOT NULL DEFAULT 'weekly',
  `minimum_payout_amount` decimal(10,2) NOT NULL DEFAULT 50.00,
  `currency` varchar(255) NOT NULL DEFAULT 'USD',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(83, 'App\\Models\\User', 11, 'auth_token', 'e8c8818aa16371cc09df8780be7fbd22423b43441a2cf1025230613d08f7a4f1', '[\"*\"]', '2025-05-31 02:54:25', NULL, '2025-05-28 18:45:06', '2025-05-31 02:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_multi_branch` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `branch_id`, `user_id`, `category_id`, `name`, `price`, `original_price`, `stock`, `sku`, `featured`, `description`, `image`, `rating`, `is_available`, `is_multi_branch`, `created_at`, `updated_at`) VALUES
(15, 4, NULL, 39, 'Elegant Maxi Dress', 54.80, NULL, 59, 'SKU7987', 0, 'High-quality Elegant Maxi Dress with excellent craftsmanship.', 'storage/products/Elegant Maxi Dress.jpg', 4.30, 1, 0, '2025-06-03 03:23:44', '2025-06-03 13:12:26'),
(16, 1, NULL, 37, 'Premium Activewear blue', 30.11, NULL, 20, 'SKU0383', 0, 'High-quality Premium Activewear blue with excellent craftsmanship.', 'storage/products/Premium Activewear blue.jpg', 3.80, 1, 0, '2025-06-03 03:23:44', '2025-06-03 13:12:26'),
(17, 4, NULL, 20, 'Classic Analog', 43.69, NULL, 62, 'SKU7688', 0, 'High-quality Classic Analog with excellent craftsmanship.', 'storage/products/Classic Analog.jpg', 4.70, 1, 0, '2025-06-03 03:23:44', '2025-06-03 13:12:26'),
(18, 6, NULL, 27, 'Oriental Perfume', 24.70, NULL, 73, 'SKU7215', 0, 'High-quality Oriental Perfume with excellent craftsmanship.', 'storage/products/Oriental Perfume.jpg', 4.50, 1, 0, '2025-06-03 03:23:44', '2025-06-03 13:12:26'),
(19, 6, NULL, 26, 'Full Coverage Foundation', 42.04, NULL, 61, 'SKU4572', 0, 'High-quality Full Coverage Foundation with excellent craftsmanship.', 'Products images/Full Coverage Foundation.jpg', 4.40, 1, 0, '2025-06-03 03:23:44', '2025-06-03 03:23:44'),
(34, 3, NULL, 37, 'Premium Activewear', 86.81, 110.31, 31, 'ACT-PRE-7171', 1, 'High-quality Activewear with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 4.20, 1, 0, '2025-06-03 03:24:29', '2025-06-03 03:24:29'),
(35, 4, NULL, 37, 'Classic Activewear', 22.66, NULL, 79, 'ACT-CLA-7148', 1, 'Traditional Activewear with timeless design and reliable quality', '/images/placeholder.jpg', 3.60, 1, 0, '2025-06-03 03:24:30', '2025-06-03 03:24:30'),
(36, 5, NULL, 38, 'Premium Bottoms (jeans, skirts)', 31.50, 122.89, 66, 'BOT-PRE-5724', 0, 'High-quality Bottoms (jeans, skirts) with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 4.80, 1, 0, '2025-06-03 03:24:31', '2025-06-03 03:24:31'),
(37, 5, NULL, 38, 'Classic Bottoms (jeans, skirts)', 59.73, NULL, 84, 'BOT-CLA-8270', 0, 'Traditional Bottoms (jeans, skirts) with timeless design and reliable quality', '/images/placeholder.jpg', 4.00, 1, 0, '2025-06-03 03:24:33', '2025-06-03 03:24:33'),
(38, 6, NULL, 39, 'Elegant Maxi Dress', 89.99, 119.99, 30, 'DRE-ELE-1750', 1, 'Flowing maxi dress perfect for special occasions', '/images/placeholder.jpg', 3.90, 1, 0, '2025-06-03 03:24:34', '2025-06-03 03:24:34'),
(39, 1, NULL, 39, 'Casual Summer Dress', 39.99, NULL, 60, 'DRE-CAS-9602', 0, 'Light and breezy dress for warm weather', '/images/placeholder.jpg', 4.20, 1, 0, '2025-06-03 03:24:41', '2025-06-03 03:24:41'),
(40, 5, NULL, 40, 'Premium Loungewear', 94.09, 89.93, 35, 'LOU-PRE-8466', 0, 'High-quality Loungewear with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 3.90, 1, 0, '2025-06-03 03:24:47', '2025-06-03 03:24:47'),
(41, 6, NULL, 40, 'Classic Loungewear', 28.34, NULL, 30, 'LOU-CLA-7445', 1, 'Traditional Loungewear with timeless design and reliable quality', '/images/placeholder.jpg', 3.60, 1, 0, '2025-06-03 03:24:49', '2025-06-03 03:24:49'),
(42, 5, NULL, 41, 'Premium Maternity wear', 59.05, 76.05, 28, 'MAT-PRE-2089', 0, 'High-quality Maternity wear with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 4.80, 1, 0, '2025-06-03 03:24:50', '2025-06-03 03:24:50'),
(43, 3, NULL, 41, 'Classic Maternity wear', 56.04, NULL, 56, 'MAT-CLA-6415', 1, 'Traditional Maternity wear with timeless design and reliable quality', '/images/placeholder.jpg', 4.80, 1, 0, '2025-06-03 03:24:55', '2025-06-03 03:24:55'),
(44, 3, NULL, 42, 'Premium Outerwear (jackets, coats)', 92.65, 125.32, 76, 'OUT-PRE-9310', 1, 'High-quality Outerwear (jackets, coats) with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 3.50, 1, 0, '2025-06-03 03:25:00', '2025-06-03 03:25:00'),
(45, 5, NULL, 42, 'Classic Outerwear (jackets, coats)', 54.94, NULL, 72, 'OUT-CLA-5887', 0, 'Traditional Outerwear (jackets, coats) with timeless design and reliable quality', '/images/placeholder.jpg', 4.90, 1, 0, '2025-06-03 03:25:01', '2025-06-03 03:25:01'),
(46, 3, NULL, 43, 'Premium Tops (blouses, tunics)', 33.75, 48.64, 77, 'TOP-PRE-7394', 0, 'High-quality Tops (blouses, tunics) with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 4.50, 1, 0, '2025-06-03 03:25:02', '2025-06-03 03:25:02'),
(47, 6, NULL, 43, 'Classic Tops (blouses, tunics)', 31.94, NULL, 34, 'TOP-CLA-5169', 0, 'Traditional Tops (blouses, tunics) with timeless design and reliable quality', '/images/placeholder.jpg', 4.50, 1, 0, '2025-06-03 03:25:04', '2025-06-03 03:25:04'),
(48, 6, NULL, 44, 'Traditional Black Abaya', 129.99, 159.99, 25, 'ABA-TRA-7827', 0, 'Classic black abaya with intricate embroidery', '/images/placeholder.jpg', 4.20, 1, 0, '2025-06-03 03:25:05', '2025-06-03 03:25:05'),
(49, 2, NULL, 45, 'Premium Kaftans', 31.83, 99.62, 73, 'KAF-PRE-5345', 0, 'High-quality Kaftans with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 4.00, 1, 0, '2025-06-03 03:25:10', '2025-06-03 03:25:10'),
(50, 6, NULL, 45, 'Classic Kaftans', 21.65, NULL, 57, 'KAF-CLA-5022', 0, 'Traditional Kaftans with timeless design and reliable quality', '/images/placeholder.jpg', 4.20, 1, 0, '2025-06-03 03:25:15', '2025-06-03 03:25:15'),
(51, 3, NULL, 46, 'Premium Salwar Kameez', 49.18, 76.08, 23, 'SAL-PRE-0150', 0, 'High-quality Salwar Kameez with excellent craftsmanship and attention to detail', '/images/placeholder.jpg', 4.50, 1, 0, '2025-06-03 03:25:19', '2025-06-03 03:25:19'),
(52, 2, NULL, 78, 'Argan Oil Shampoo', 45.04, NULL, 21, 'SHAARG061', 0, 'Classic shampoos with timeless design and reliable quality.', 'storage/products/Argan Oil Shampoo.jpg', 4.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(53, 5, NULL, 53, 'Athletic Running Sneakers', 112.99, NULL, 70, 'SNEATH648', 0, 'Classic sneakers with timeless design and reliable quality. Available in beautiful blue color.', 'Products images/Athletic Running Sneakers blue.jpg', 4.60, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(54, 3, NULL, 76, 'Calssic Conditioners', 17.34, NULL, 72, 'CONCAL752', 0, 'Traditional conditioners perfect for everyday wear.', 'storage/products/Calssic Conditioners.jpg', 5.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(55, 4, NULL, 53, 'Casual Canvas Sneakers balck', 52.33, NULL, 33, 'SNECAS228', 0, 'Comfortable sneakers with classic styling.', 'Products images/Casual Canvas Sneakers balck.jpg', 4.40, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(56, 6, NULL, 53, 'Casual Canvas Sneakers', 89.25, NULL, 15, 'SNECAS904', 0, 'Traditional sneakers perfect for everyday wear. Available in beautiful green color.', 'Products images/Casual Canvas Sneakers green.jpg', 3.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(57, 4, NULL, 39, 'Casual Summer Dress sky', 86.97, NULL, 48, 'DRECAS123', 0, 'Classic dresses with timeless design and reliable quality.', 'Products images/Casual Summer Dress sky.jpg', 4.60, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(58, 5, NULL, 37, 'Activewear', 72.86, NULL, 85, 'ACTACT614', 0, 'Traditional activewear perfect for everyday wear. Available in beautiful gray color.', 'storage/products/Classic Activewear gray.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(59, 2, NULL, 102, 'Analog', 28.56, NULL, 24, 'ANAANA163', 0, 'Classic analog with timeless design and reliable quality.', 'storage/products/Classic Analog.jpg', 4.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(60, 2, NULL, 61, 'Anklets', 33.80, NULL, 80, 'ANKANK492', 0, 'Classic anklets with timeless design and reliable quality.', 'storage/products/Classic Anklets.jpg', 4.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(61, 5, NULL, 95, 'Baby carriers', 29.29, NULL, 36, 'BABBAB666', 0, 'Classic baby carriers with timeless design and reliable quality.', 'Products images/Classic Baby carriers.jpg', 3.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(62, 6, NULL, 58, 'Backpacks', 76.97, NULL, 53, 'BACBAC192', 0, 'Comfortable backpacks with classic styling. Available in beautiful blue color.', 'Products images/Classic Backpacks blue.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(63, 1, NULL, 89, 'Belly support belts', 24.57, NULL, 19, 'BELBEL137', 0, 'Comfortable belly support belts with classic styling. Available in beautiful black color.', 'storage/products/Classic Belly support belts black.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(64, 5, NULL, 54, 'Belts bk', 25.88, NULL, 61, 'BELBEL672', 0, 'Traditional belts perfect for everyday wear.', 'Products images/Classic Belts bk.jpg', 3.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(65, 5, NULL, 54, 'Belts', 19.54, NULL, 73, 'BELBEL833', 0, 'Classic belts with timeless design and reliable quality. Available in beautiful brown color.', 'storage/products/Classic Belts brown.jpg', 4.20, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(66, 1, NULL, 66, 'Blushes', 33.20, NULL, 63, 'BLUBLU831', 1, 'Traditional blushes perfect for everyday wear.', 'storage/products/Classic Blushes.jpg', 4.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(67, 6, NULL, 82, 'Body mists', 42.70, NULL, 73, 'BODBOD393', 1, 'Comfortable body mists with classic styling.', 'Products images/Classic Body mists.jpg', 3.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(68, 2, NULL, 49, 'Boots', 104.88, NULL, 62, 'BOOBOO894', 1, 'Comfortable boots with classic styling. Available in beautiful brown color.', 'storage/products/Classic Boots brown.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(69, 6, NULL, 98, 'Bottles', 47.51, NULL, 16, 'BOTBOT493', 0, 'Classic bottles with timeless design and reliable quality.', 'storage/products/Classic Bottles.jpg', 4.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(70, 2, NULL, 38, 'Bottoms (jeans, skirts)', 74.04, NULL, 27, 'BOTBOT610', 0, 'Traditional bottoms (jeans, skirts) perfect for everyday wear. Available in beautiful black color.', 'storage/products/Classic Bottoms (jeans, skirts) black.jpg', 4.60, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(71, 3, NULL, 62, 'Bracelets', 34.40, NULL, 28, 'BRABRA939', 0, 'Classic bracelets with timeless design and reliable quality.', 'Products images/Classic Bracelets.jpg', 4.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(72, 2, NULL, 85, 'Bras', 40.32, NULL, 59, 'BRABRA662', 1, 'Classic bras with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Classic Bras black.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(73, 4, NULL, 99, 'Breast pumps', 58.78, NULL, 36, 'BREBRE338', 0, 'Traditional breast pumps perfect for everyday wear.', 'storage/products/Classic Breast pumps.jpg', 5.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(74, 5, NULL, 96, 'Car seats', 50.66, NULL, 16, 'CARCAR497', 0, 'Traditional car seats perfect for everyday wear.', 'Products images/Classic Car seats.jpg', 3.60, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(75, 2, NULL, 71, 'Cleansers', 18.00, NULL, 82, 'CLECLE300', 1, 'Classic cleansers with timeless design and reliable quality.', 'storage/products/Classic Cleansers.jpg', 4.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(76, 1, NULL, 59, 'Crossbody bags', 116.53, NULL, 68, 'CROCRO817', 0, 'Traditional crossbody bags perfect for everyday wear. Available in beautiful brown color.', 'storage/products/Classic Crossbody bags brown.jpg', 3.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(77, 6, NULL, 83, 'Deodorants', 28.03, NULL, 72, 'DEODEO680', 0, 'Comfortable deodorants with classic styling.', 'storage/products/Classic Deodorants.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(78, 5, NULL, 103, 'Digital', 37.06, NULL, 77, 'DIGDIG764', 0, 'Comfortable digital with classic styling.', 'storage/products/Classic Digital.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(79, 5, NULL, 63, 'Earrings', 37.91, NULL, 42, 'EAREAR129', 0, 'Classic earrings with timeless design and reliable quality.', 'Products images/Classic Earrings.jpg', 3.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(80, 4, NULL, 67, 'Eyeshadows', 13.36, NULL, 46, 'EYEEYE961', 0, 'Classic eyeshadows with timeless design and reliable quality.', 'Products images/Classic Eyeshadows.jpg', 4.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(81, 4, NULL, 50, 'Flats', 37.67, NULL, 66, 'FLAFLA590', 0, 'Classic flats with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Classic Flats black.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(82, 4, NULL, 77, 'Hair oils', 33.31, NULL, 53, 'HAIHAI438', 0, 'Comfortable hair oils with classic styling.', 'Products images/Classic Hair oils.jpg', 3.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(83, 6, NULL, 55, 'Hats', 35.81, NULL, 56, 'HATHAT581', 1, 'Classic hats with timeless design and reliable quality.', 'storage/products/Classic Hats.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(84, 1, NULL, 100, 'High chairs', 24.82, NULL, 81, 'HIGHIG811', 1, 'Traditional high chairs perfect for everyday wear.', 'storage/products/Classic High chairs.jpg', 4.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(85, 3, NULL, 45, 'Kaftans', 47.94, NULL, 85, 'KAFKAF454', 0, 'Comfortable kaftans with classic styling. Available in beautiful darkorange color.', 'Products images/Classic Kaftans darkorange.jpg', 3.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(86, 6, NULL, 69, 'Lipsticks', 24.68, NULL, 44, 'LIPLIP598', 0, 'Classic lipsticks with timeless design and reliable quality.', 'Products images/Classic Lipsticks.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(87, 2, NULL, 40, 'Loungewear', 23.85, NULL, 26, 'LOULOU267', 0, 'Comfortable loungewear with classic styling. Available in beautiful black color.', 'storage/products/Classic Loungewear black.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(88, 3, NULL, 70, 'Mascaras', 26.86, NULL, 37, 'MASMAS278', 0, 'Classic mascaras with timeless design and reliable quality.', 'storage/products/Classic Mascaras.jpg', 4.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(89, 1, NULL, 90, 'Maternity clothing', 44.19, NULL, 54, 'MATMAT273', 0, 'Classic maternity clothing with timeless design and reliable quality. Available in beautiful blue color.', 'storage/products/Classic Maternity clothing blue.jpg', 4.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(90, 3, NULL, 41, 'Maternity wear', 39.53, NULL, 66, 'MATMAT118', 0, 'Comfortable maternity wear with classic styling. Available in beautiful black color.', 'storage/products/Classic Maternity wear black.jpg', 3.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(91, 4, NULL, 73, 'Moisturizers', 21.23, NULL, 17, 'MOIMOI004', 0, 'Comfortable moisturizers with classic styling.', 'Products images/Classic Moisturizers.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(92, 5, NULL, 64, 'Necklaces', 52.69, NULL, 53, 'NECNEC258', 1, 'Classic necklaces with timeless design and reliable quality.', 'Products images/Classic Necklaces.jpg', 3.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(93, 6, NULL, 91, 'Nursing bras', 33.67, NULL, 68, 'NURNUR262', 1, 'Classic nursing bras with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Classic Nursing bras black.jpg', 5.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(94, 2, NULL, 92, 'Onesies', 20.30, NULL, 58, 'ONEONE437', 0, 'Comfortable onesies with classic styling. Available in beautiful lightgreen color.', 'storage/products/Classic Onesies lightgreen.jpg', 4.40, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(95, 3, NULL, 92, 'Onesies likered', 30.15, NULL, 48, 'ONEONE112', 0, 'Classic onesies with timeless design and reliable quality.', 'Products images/Classic Onesies likered.jpg', 4.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(96, 4, NULL, 42, 'Outerwear (jackets, coats)', 53.94, NULL, 54, 'OUTOUT218', 0, 'Comfortable outerwear (jackets, coats) with classic styling. Available in beautiful black color.', 'storage/products/Classic Outerwear (jackets, coats) black.jpg', 4.60, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(97, 5, NULL, 42, 'Outerwear balck', 86.58, NULL, 65, 'OUTOUT490', 0, 'Comfortable outerwear (jackets, coats) with classic styling.', 'storage/products/Classic Outerwear balck.jpg', 4.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(98, 4, NULL, 42, 'Outerwear', 80.10, NULL, 36, 'OUTOUT890', 0, 'Classic outerwear (jackets, coats) with timeless design and reliable quality.', 'storage/products/Classic Outerwear.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(99, 4, NULL, 87, 'Panties', 56.03, NULL, 44, 'PANPAN983', 0, 'Traditional panties perfect for everyday wear. Available in beautiful black color.', 'storage/products/Classic Panties black.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(100, 1, NULL, 48, 'Pray Clothes', 35.39, NULL, 72, 'PRAPRA050', 0, 'Classic pray clothes with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Classic Pray Clothes black.jpg', 3.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(101, 2, NULL, 48, 'Pray Clothes lightggreen', 18.42, NULL, 65, 'PRAPRA731', 0, 'Classic pray clothes with timeless design and reliable quality.', 'storage/products/Classic Pray Clothes lightggreen.jpg', 4.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(102, 2, NULL, 65, 'Rings', 30.43, NULL, 33, 'RINRIN277', 0, 'Classic rings with timeless design and reliable quality.', 'storage/products/Classic Rings.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(103, 2, NULL, 46, 'Salwar Kameez', 32.48, NULL, 84, 'SALSAL792', 0, 'Classic salwar kameez with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Classic Salwar Kameez black.jpg', 4.30, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(104, 6, NULL, 47, 'Sarees bpink', 58.70, NULL, 37, 'SARSAR092', 1, 'Traditional sarees perfect for everyday wear.', 'storage/products/Classic Sarees bpink.jpg', 4.40, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(105, 2, NULL, 47, 'Sarees', 37.75, NULL, 78, 'SARSAR632', 0, 'Traditional sarees perfect for everyday wear. Available in beautiful orange color.', 'storage/products/Classic Sarees orange.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(106, 2, NULL, 56, 'Scarves', 40.52, NULL, 15, 'SCASCA694', 0, 'Traditional scarves perfect for everyday wear. Available in beautiful black color.', 'storage/products/Classic Scarves black.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(107, 6, NULL, 81, 'Scrunchies', 21.55, NULL, 32, 'SCRSCR977', 0, 'Comfortable scrunchies with classic styling. Available in beautiful black color.', 'storage/products/Classic Scrunchies black.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(108, 5, NULL, 74, 'Serums', 39.43, NULL, 15, 'SERSER757', 0, 'Classic serums with timeless design and reliable quality.', 'Products images/Classic Serums.jpg', 4.30, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(109, 2, NULL, 88, 'Shapewear', 16.07, NULL, 74, 'SHASHA612', 0, 'Comfortable shapewear with classic styling. Available in beautiful brown color.', 'storage/products/Classic Shapewear brown.jpg', 4.40, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(110, 4, NULL, 104, 'Smartwatches', 24.37, NULL, 15, 'SMASMA920', 1, 'Comfortable smartwatches with classic styling.', 'storage/products/Classic Smartwatches.jpg', 3.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(111, 1, NULL, 101, 'Sterilizers', 20.53, NULL, 68, 'STESTE679', 0, 'Classic sterilizers with timeless design and reliable quality.', 'storage/products/Classic Sterilizers.jpg', 5.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(112, 3, NULL, 51, 'Stiletto Heels', 48.93, NULL, 70, 'HEESTI143', 0, 'Classic heels with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Classic Stiletto Heels black.jpg', 3.70, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(113, 5, NULL, 97, 'Strollers', 32.19, NULL, 61, 'STRSTR741', 1, 'Comfortable strollers with classic styling.', 'Products images/Classic Strollers.jpg', 4.20, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(114, 5, NULL, 57, 'Sunglasses', 68.03, NULL, 28, 'SUNSUN761', 1, 'Comfortable sunglasses with classic styling. Available in beautiful black color.', 'Products images/Classic Sunglasses black.jpg', 3.90, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(115, 5, NULL, 75, 'Sunscreens', 34.46, NULL, 41, 'SUNSUN888', 1, 'Comfortable sunscreens with classic styling.', 'Products images/Classic Sunscreens.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(116, 5, NULL, 43, 'Tops (blouses, tunics)', 27.30, NULL, 71, 'TOPTOP375', 0, 'Comfortable tops (blouses, tunics) with classic styling. Available in beautiful black color.', 'storage/products/Classic Tops (blouses, tunics) black.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(117, 6, NULL, 60, 'Tote bags Cyan', 78.40, NULL, 78, 'TOTTOT219', 0, 'Classic tote bags with timeless design and reliable quality. Available in beautiful cyan color.', 'Products images/Classic Tote bags Cyan.jpg', 3.80, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(118, 1, NULL, 60, 'Tote bags', 78.22, NULL, 31, 'TOTTOT395', 0, 'Traditional tote bags perfect for everyday wear. Available in beautiful blue color.', 'storage/products/Classic Tote bags blue.jpg', 4.30, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(119, 6, NULL, 48, 'Cotton Everyday Hijab', 52.06, NULL, 30, 'PRACOT589', 0, 'Traditional pray clothes perfect for everyday wear. Available in beautiful black color.', 'storage/products/Cotton Everyday Hijab black.jpg', 5.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(120, 4, NULL, 68, 'Full Coverage Foundation', 19.92, NULL, 53, 'FOUFUL809', 0, 'Classic foundations with timeless design and reliable quality.', 'Products images/Full Coverage Foundation.jpg', 4.10, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(121, 5, NULL, 59, 'Leather Handbag', 45.94, NULL, 54, 'CROLEA736', 0, 'Comfortable crossbody bags with classic styling. Available in beautiful brown color.', 'Products images/Leather Handbag brown.jpg', 4.40, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(122, 6, NULL, 59, 'Luxury Leather Handbag', 63.79, NULL, 84, 'CROLUX491', 0, 'Comfortable crossbody bags with classic styling. Available in beautiful black color.', 'Products images/Luxury Leather Handbag black.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(123, 2, NULL, 39, 'Maxi Dress', 59.50, NULL, 71, 'DREMAX503', 0, 'Traditional dresses perfect for everyday wear. Available in beautiful cyan color.', 'storage/products/Maxi Dress cyan.jpg', 4.50, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(124, 4, NULL, 84, 'Oriental Perfume', 50.05, NULL, 65, 'PERORI518', 0, 'Classic perfumes with timeless design and reliable quality.', 'storage/products/Oriental Perfume.jpg', 4.00, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(125, 6, NULL, 84, 'Oriental Rose Perfume', 38.55, NULL, 50, 'PERORI112', 0, 'Traditional perfumes perfect for everyday wear.', 'Products images/Oriental Rose Perfume.jpg', 4.20, 1, 0, '2025-06-03 03:27:47', '2025-06-03 03:27:47'),
(126, 3, NULL, 37, 'Activewear bluedark', 60.08, 78.10, 69, 'ACTACT655', 0, 'Premium activewear featuring superior craftsmanship and elegant design.', 'storage/products/Premium Activewear bluedark.jpg', 4.20, 1, 0, '2025-06-03 03:27:47', '2025-06-03 13:12:26'),
(127, 2, NULL, 79, 'Clips', 81.57, 106.04, 54, 'CLICLI548', 0, 'Premium clips featuring superior craftsmanship and elegant design. Available in beautiful black color.', 'storage/products/Premium Clips black.jpg', 3.70, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(128, 2, NULL, 76, 'Conditioners', 50.66, 65.86, 26, 'CONCON969', 0, 'High-quality conditioners crafted with premium materials and attention to detail.', 'storage/products/Premium Conditioners.jpg', 4.80, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(129, 3, NULL, 72, 'Face masks', 86.84, 112.89, 26, 'FACFAC238', 0, 'Premium face masks featuring superior craftsmanship and elegant design.', 'Products images/Premium Face masks.jpg', 4.60, 1, 0, '2025-06-03 03:27:48', '2025-06-03 03:27:48'),
(130, 2, NULL, 80, 'Hairbands', 82.17, 106.82, 72, 'HAIHAI771', 0, 'Luxurious hairbands designed for comfort and style. Available in beautiful black color.', 'storage/products/Premium Hairbands black.jpg', 3.80, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(131, 4, NULL, 55, 'Hat', 40.77, 53.00, 33, 'HATHAT829', 0, 'High-quality hats crafted with premium materials and attention to detail.', 'storage/products/Premium Hat.jpg', 4.20, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(132, 4, NULL, 45, 'Kaftans light', 86.81, 112.85, 72, 'KAFKAF923', 0, 'Luxurious kaftans designed for comfort and style. Available in beautiful blue color.', 'storage/products/Premium Kaftans light  blue.jpg', 3.50, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(133, 6, NULL, 86, 'Lingerie', 22.53, 29.29, 82, 'LINLIN288', 0, 'Premium lingerie featuring superior craftsmanship and elegant design. Available in beautiful black color.', 'storage/products/Premium Lingerie black.jpg', 3.60, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(134, 6, NULL, 90, 'Maternity clothing green dark', 118.05, 153.47, 15, 'MATMAT142', 0, 'Luxurious maternity clothing designed for comfort and style.', 'storage/products/Premium Maternity clothing green dark.jpg', 4.60, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(135, 2, NULL, 42, 'Outerwear light', 148.41, 192.93, 79, 'OUTOUT001', 0, 'Luxurious outerwear (jackets, coats) designed for comfort and style. Available in beautiful green color.', 'storage/products/Premium Outerwear light green.jpg', 4.80, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(136, 6, NULL, 42, 'Outerwear yellowcoo', 138.68, 180.28, 22, 'OUTOUT482', 0, 'Premium outerwear (jackets, coats) featuring superior craftsmanship and elegant design.', 'storage/products/Premium Outerwear yellowcoo.jpg', 3.90, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(137, 5, NULL, 46, 'Salwar Kameez bluegray', 82.70, 107.51, 64, 'SALSAL017', 1, 'High-quality salwar kameez crafted with premium materials and attention to detail.', 'storage/products/Premium Salwar Kameez bluegray.jpg', 4.60, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(138, 4, NULL, 52, 'Sandals', 80.55, 104.72, 32, 'SANSAN897', 1, 'Luxurious sandals designed for comfort and style. Available in beautiful black color.', 'storage/products/Premium Sandals black.jpg', 3.60, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(139, 3, NULL, 48, 'Silk Hijab', 46.85, 60.91, 21, 'PRASIL481', 0, 'Luxurious pray clothes designed for comfort and style.', 'storage/products/Premium Silk Hijab.jpg', 3.80, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(140, 4, NULL, 60, 'Tote bags light', 89.34, 116.14, 62, 'TOTTOT068', 1, 'Luxurious tote bags designed for comfort and style. Available in beautiful blue color.', 'storage/products/Premium Tote bags light blue.jpg', 3.50, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(141, 1, NULL, 53, 'Running Sneakers', 51.21, NULL, 75, 'SNERUN349', 0, 'Classic sneakers with timeless design and reliable quality. Available in beautiful black color.', 'storage/products/Running Sneakers black.jpg', 4.50, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(142, 5, NULL, 58, 'sic Backpacks', 46.54, NULL, 51, 'BACSIC490', 0, 'Comfortable backpacks with classic styling. Available in beautiful black color.', 'storage/products/sic Backpacks black.jpg', 4.60, 1, 0, '2025-06-03 03:27:48', '2025-06-03 13:12:26'),
(143, 6, NULL, 53, 'Athletic Running Sneakers', 29.62, NULL, 61, 'SKU5441', 0, 'High-quality Athletic Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Athletic Running Sneakers blue.jpg', 4.80, 1, 0, '2025-06-03 03:28:52', '2025-06-03 03:28:52'),
(144, 2, NULL, 53, 'Athletic Running Sneakers', 53.29, NULL, 48, 'SKU1843', 1, 'High-quality Athletic Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'storage/products/Athletic Running Sneakers orange.jpg', 4.70, 1, 0, '2025-06-03 03:28:52', '2025-06-03 13:12:26'),
(145, 2, NULL, 53, 'Athletic Running Sneakers', 56.43, NULL, 44, 'SKU5577', 0, 'High-quality Athletic Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful violet color.', 'storage/products/Athletic Running Sneakers violet.jpg', 4.30, 1, 0, '2025-06-03 03:28:52', '2025-06-03 13:12:26'),
(146, 5, NULL, 53, 'Athletic Running Sneakers', 25.32, NULL, 70, 'SKU9468', 0, 'High-quality Athletic Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'Athletic Running Sneakers white.jpg', 4.00, 1, 0, '2025-06-03 03:28:52', '2025-06-03 03:28:52'),
(147, 5, NULL, 53, 'Casual Canvas Sneakers', 73.85, NULL, 66, 'SKU9660', 1, 'High-quality Casual Canvas Sneakers with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'Casual Canvas Sneakers green.jpg', 3.60, 1, 0, '2025-06-03 03:28:52', '2025-06-03 03:28:52'),
(148, 5, NULL, 39, 'Casual Summer Dress darkblue', 42.98, NULL, 78, 'SKU8349', 0, 'High-quality Casual Summer Dress darkblue with excellent craftsmanship and attention to detail.', 'Casual Summer Dress darkblue.jpg', 4.80, 1, 0, '2025-06-03 03:28:52', '2025-06-03 03:28:52'),
(149, 4, NULL, 39, 'Casual Summer Dress', 52.36, NULL, 39, 'SKU6005', 0, 'High-quality Casual Summer Dress with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Casual Summer Dress white.jpg', 4.90, 1, 0, '2025-06-03 03:28:52', '2025-06-03 13:12:26'),
(150, 1, NULL, 39, 'Casual Summer Dress', 62.61, NULL, 22, 'SKU7550', 0, 'High-quality Casual Summer Dress with excellent craftsmanship and attention to detail. Available in beautiful yellow color.', 'storage/products/Casual Summer Dress yellow.jpg', 4.60, 1, 0, '2025-06-03 03:28:52', '2025-06-03 13:12:26'),
(151, 3, NULL, 37, 'Activewear', 25.96, NULL, 77, 'SKU2076', 1, 'High-quality Activewear with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Classic Activewear gray.jpg', 3.70, 1, 0, '2025-06-03 03:28:52', '2025-06-03 13:12:26'),
(152, 6, NULL, 37, 'Activewear', 75.28, NULL, 51, 'SKU7436', 1, 'High-quality Activewear with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Classic Activewear green.jpg', 4.90, 1, 0, '2025-06-03 03:28:52', '2025-06-03 13:12:26'),
(153, 6, NULL, 37, 'Activewear', 63.02, NULL, 32, 'SKU8215', 0, 'High-quality Activewear with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Classic Activewear white.jpg', 4.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(154, 3, NULL, 92, 'Anklets', 36.38, NULL, 44, 'SKU3995', 1, 'High-quality Anklets with excellent craftsmanship and attention to detail.', 'storage/products/Classic Anklets.jpg', 3.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(155, 4, NULL, 85, 'Baby carriers', 74.26, NULL, 60, 'SKU1537', 0, 'High-quality Baby carriers with excellent craftsmanship and attention to detail.', 'Classic Baby carriers.jpg', 5.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(156, 3, NULL, 58, 'Backpacks', 37.80, NULL, 72, 'SKU6960', 0, 'High-quality Backpacks with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Classic Backpacks blue.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(157, 2, NULL, 58, 'Backpacks', 61.80, NULL, 63, 'SKU6043', 0, 'High-quality Backpacks with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Classic Backpacks red.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(158, 3, NULL, 54, 'Belly support belts', 49.84, NULL, 78, 'SKU9547', 1, 'High-quality Belly support belts with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Belly support belts black.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(159, 5, NULL, 54, 'Belly support belts', 24.52, NULL, 61, 'SKU1142', 0, 'High-quality Belly support belts with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Belly support belts blue.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(160, 6, NULL, 54, 'Belly support belts', 75.89, NULL, 60, 'SKU9169', 0, 'High-quality Belly support belts with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Belly support belts red.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(161, 3, NULL, 54, 'Belts bk', 39.31, NULL, 23, 'SKU8542', 0, 'High-quality Belts bk with excellent craftsmanship and attention to detail.', 'Classic Belts bk.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(162, 2, NULL, 54, 'Belts', 77.03, NULL, 41, 'SKU1134', 0, 'High-quality Belts with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'storage/products/Classic Belts brown.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(163, 1, NULL, 61, 'Blushes', 71.70, NULL, 38, 'SKU1067', 0, 'High-quality Blushes with excellent craftsmanship and attention to detail.', 'storage/products/Classic Blushes.jpg', 4.40, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(164, 3, NULL, 82, 'Body mists', 72.28, NULL, 49, 'SKU2589', 0, 'High-quality Body mists with excellent craftsmanship and attention to detail.', 'Classic Body mists.jpg', 3.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(165, 2, NULL, 49, 'Boots', 63.31, NULL, 35, 'SKU3893', 0, 'High-quality Boots with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'storage/products/Classic Boots brown.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(166, 6, NULL, 49, 'Boots', 33.87, NULL, 29, 'SKU7737', 0, 'High-quality Boots with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Classic Boots green.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(167, 4, NULL, 49, 'Boots', 35.83, NULL, 77, 'SKU7430', 0, 'High-quality Boots with excellent craftsmanship and attention to detail. Available in beautiful violet color.', 'storage/products/Classic Boots violet.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(168, 6, NULL, 98, 'Bottles', 52.61, NULL, 39, 'SKU5413', 1, 'High-quality Bottles with excellent craftsmanship and attention to detail.', 'storage/products/Classic Bottles.jpg', 5.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(169, 4, NULL, 50, 'Bottoms (jeans, skirts)', 61.64, NULL, 76, 'SKU0610', 0, 'High-quality Bottoms (jeans, skirts) with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Bottoms (jeans, skirts) black.jpg', 4.40, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(170, 2, NULL, 44, 'Bottoms (jeans, skirts)', 28.73, NULL, 71, 'SKU3331', 0, 'High-quality Bottoms (jeans, skirts) with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Bottoms (jeans, skirts) blue.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(171, 3, NULL, 64, 'Bottoms (jeans, skirts)', 27.83, NULL, 34, 'SKU0161', 0, 'High-quality Bottoms (jeans, skirts) with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'Classic Bottoms (jeans, skirts) gray.jpg', 4.10, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(172, 6, NULL, 62, 'Bracelets', 39.99, NULL, 53, 'SKU1316', 0, 'High-quality Bracelets with excellent craftsmanship and attention to detail.', 'Classic Bracelets.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(173, 5, NULL, 85, 'Bras', 61.62, NULL, 25, 'SKU2221', 1, 'High-quality Bras with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Bras black.jpg', 4.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:26'),
(174, 3, NULL, 85, 'Bras', 59.84, NULL, 37, 'SKU2898', 0, 'High-quality Bras with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Classic Bras blue.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(175, 5, NULL, 85, 'Bras', 59.36, NULL, 20, 'SKU5957', 1, 'High-quality Bras with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Classic Bras red.jpg', 5.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(176, 4, NULL, 66, 'Breast pumps', 45.05, NULL, 28, 'SKU5396', 0, 'High-quality Breast pumps with excellent craftsmanship and attention to detail.', 'storage/products/Classic Breast pumps.jpg', 4.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(177, 5, NULL, 96, 'Car seats', 38.55, NULL, 29, 'SKU7958', 0, 'High-quality Car seats with excellent craftsmanship and attention to detail.', 'Classic Car seats.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(178, 3, NULL, 76, 'Cleansers', 41.70, NULL, 20, 'SKU5469', 0, 'High-quality Cleansers with excellent craftsmanship and attention to detail.', 'storage/products/Classic Cleansers.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(180, 4, NULL, 37, 'Crossbody bags', 38.31, NULL, 60, 'SKU3601', 0, 'High-quality Crossbody bags with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'storage/products/Classic Crossbody bags orange.jpg', 4.10, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(181, 1, NULL, 84, 'Deodorants', 67.56, NULL, 79, 'SKU7511', 0, 'High-quality Deodorants with excellent craftsmanship and attention to detail.', 'storage/products/Classic Deodorants.jpg', 4.20, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(182, 2, NULL, 103, 'Digital', 55.15, NULL, 64, 'SKU9618', 0, 'High-quality Digital with excellent craftsmanship and attention to detail.', 'storage/products/Classic Digital.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(183, 5, NULL, 63, 'Earrings', 34.70, NULL, 31, 'SKU9313', 0, 'High-quality Earrings with excellent craftsmanship and attention to detail.', 'Classic Earrings.jpg', 3.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(184, 5, NULL, 80, 'Eyeshadows', 72.70, NULL, 30, 'SKU3503', 0, 'High-quality Eyeshadows with excellent craftsmanship and attention to detail.', 'Classic Eyeshadows.jpg', 3.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(185, 2, NULL, 50, 'Flats', 65.16, NULL, 34, 'SKU0691', 0, 'High-quality Flats with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Flats black.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(186, 4, NULL, 50, 'Flats', 26.67, NULL, 78, 'SKU2710', 0, 'High-quality Flats with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Flats blue.jpg', 5.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(187, 6, NULL, 50, 'Flats', 51.30, NULL, 68, 'SKU6848', 1, 'High-quality Flats with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Classic Flats red.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(188, 6, NULL, 54, 'Hair oils', 79.52, NULL, 52, 'SKU4770', 0, 'High-quality Hair oils with excellent craftsmanship and attention to detail.', 'Classic Hair oils.jpg', 4.20, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(189, 1, NULL, 55, 'Hats', 70.20, NULL, 48, 'SKU6353', 0, 'High-quality Hats with excellent craftsmanship and attention to detail.', 'storage/products/Classic Hats.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(190, 4, NULL, 96, 'High chairs', 26.62, NULL, 56, 'SKU1669', 0, 'High-quality High chairs with excellent craftsmanship and attention to detail.', 'storage/products/Classic High chairs.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(191, 3, NULL, 58, 'Kaftans darkorange', 47.22, NULL, 60, 'SKU4362', 0, 'High-quality Kaftans darkorange with excellent craftsmanship and attention to detail.', 'Classic Kaftans darkorange.jpg', 4.40, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(192, 1, NULL, 96, 'Kaftans', 54.11, NULL, 32, 'SKU6860', 1, 'High-quality Kaftans with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'storage/products/Classic Kaftans orange.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:28'),
(193, 5, NULL, 69, 'Lipsticks', 66.82, NULL, 42, 'SKU3552', 0, 'High-quality Lipsticks with excellent craftsmanship and attention to detail.', 'Classic Lipsticks.jpg', 5.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(195, 6, NULL, 85, 'Loungewear', 67.56, NULL, 72, 'SKU7402', 0, 'High-quality Loungewear with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Classic Loungewear blue.jpg', 4.10, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(196, 2, NULL, 75, 'Loungewear', 65.63, NULL, 61, 'SKU1215', 0, 'High-quality Loungewear with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'storage/products/Classic Loungewear orange.jpg', 3.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(197, 2, NULL, 78, 'Mascaras', 72.76, NULL, 39, 'SKU0120', 1, 'High-quality Mascaras with excellent craftsmanship and attention to detail.', 'storage/products/Classic Mascaras.jpg', 5.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(198, 1, NULL, 84, 'Maternity clothing', 37.35, NULL, 69, 'SKU7045', 0, 'High-quality Maternity clothing with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Maternity clothing blue.jpg', 4.10, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(199, 3, NULL, 82, 'Maternity clothing', 63.92, NULL, 75, 'SKU1318', 0, 'High-quality Maternity clothing with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Maternity clothing red.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(200, 3, NULL, 43, 'Maternity clothing', 38.35, NULL, 75, 'SKU8024', 0, 'High-quality Maternity clothing with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Classic Maternity clothing white.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(201, 6, NULL, 43, 'Maternity clothing', 63.85, NULL, 49, 'SKU4183', 0, 'High-quality Maternity clothing with excellent craftsmanship and attention to detail.', 'storage/products/Classic Maternity clothing.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(202, 5, NULL, 101, 'Maternity wear', 62.22, NULL, 51, 'SKU3526', 0, 'High-quality Maternity wear with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Maternity wear black.jpg', 4.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(203, 1, NULL, 72, 'Maternity wear', 27.39, NULL, 54, 'SKU4400', 0, 'High-quality Maternity wear with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Classic Maternity wear red.jpg', 4.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(204, 3, NULL, 60, 'Moisturizers', 31.00, NULL, 71, 'SKU4855', 0, 'High-quality Moisturizers with excellent craftsmanship and attention to detail.', 'Classic Moisturizers.jpg', 3.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(205, 5, NULL, 64, 'Necklaces', 34.48, NULL, 47, 'SKU6221', 0, 'High-quality Necklaces with excellent craftsmanship and attention to detail.', 'Classic Necklaces.jpg', 3.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(206, 2, NULL, 85, 'Nursing bras', 38.77, NULL, 44, 'SKU9523', 1, 'High-quality Nursing bras with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Nursing bras black.jpg', 4.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(207, 2, NULL, 85, 'Nursing bras', 76.37, NULL, 39, 'SKU3761', 1, 'High-quality Nursing bras with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Classic Nursing bras green.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(208, 5, NULL, 85, 'Nursing bras', 50.11, NULL, 51, 'SKU0487', 0, 'High-quality Nursing bras with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Nursing bras red.jpg', 4.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(209, 6, NULL, 92, 'Onesies lightgreen', 61.11, NULL, 79, 'SKU0746', 0, 'High-quality Onesies lightgreen with excellent craftsmanship and attention to detail.', 'storage/products/Classic Onesies lightgreen.jpg', 3.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(210, 6, NULL, 92, 'Onesies likered', 42.15, NULL, 30, 'SKU7212', 0, 'High-quality Onesies likered with excellent craftsmanship and attention to detail.', 'Classic Onesies likered.jpg', 4.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(211, 2, NULL, 92, 'Onesies', 24.90, NULL, 78, 'SKU2877', 1, 'High-quality Onesies with excellent craftsmanship and attention to detail. Available in beautiful violet color.', 'storage/products/Classic Onesies violet.jpg', 4.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(212, 2, NULL, 65, 'Outerwear (jackets, coats)', 27.85, NULL, 53, 'SKU1842', 0, 'High-quality Outerwear (jackets, coats) with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Outerwear (jackets, coats) black.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(214, 5, NULL, 70, 'Outerwear balck', 39.69, NULL, 22, 'SKU7693', 0, 'High-quality Outerwear balck with excellent craftsmanship and attention to detail.', 'storage/products/Classic Outerwear balck.jpg', 4.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(215, 1, NULL, 88, 'Outerwear', 45.53, NULL, 60, 'SKU3681', 0, 'High-quality Outerwear with excellent craftsmanship and attention to detail.', 'storage/products/Classic Outerwear.jpg', 3.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(216, 1, NULL, 87, 'Panties', 53.93, NULL, 72, 'SKU8940', 1, 'High-quality Panties with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Panties black.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(217, 4, NULL, 87, 'Panties', 73.37, NULL, 22, 'SKU2095', 0, 'High-quality Panties with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'Classic Panties white.jpg', 3.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(218, 1, NULL, 59, 'Pray Clothes', 74.29, NULL, 59, 'SKU3390', 0, 'High-quality Pray Clothes with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Pray Clothes black.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(219, 5, NULL, 95, 'Pray Clothes lightggreen', 77.59, NULL, 45, 'SKU5849', 0, 'High-quality Pray Clothes lightggreen with excellent craftsmanship and attention to detail.', 'storage/products/Classic Pray Clothes lightggreen.jpg', 3.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(220, 5, NULL, 92, 'Pray Clothes', 54.35, NULL, 46, 'SKU0885', 0, 'High-quality Pray Clothes with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'Classic Pray Clothes white.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(221, 1, NULL, 65, 'Rings', 26.46, NULL, 32, 'SKU9926', 0, 'High-quality Rings with excellent craftsmanship and attention to detail.', 'storage/products/Classic Rings.jpg', 3.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(222, 3, NULL, 81, 'Salwar Kameez', 39.21, NULL, 47, 'SKU8396', 0, 'High-quality Salwar Kameez with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Salwar Kameez black.jpg', 4.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(223, 4, NULL, 52, 'Salwar Kameez', 44.16, NULL, 22, 'SKU8212', 0, 'High-quality Salwar Kameez with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Salwar Kameez blue.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(224, 6, NULL, 104, 'Salwar Kameez', 21.39, NULL, 23, 'SKU3362', 0, 'High-quality Salwar Kameez with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Salwar Kameez red.jpg', 4.10, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53');
INSERT INTO `products` (`id`, `branch_id`, `user_id`, `category_id`, `name`, `price`, `original_price`, `stock`, `sku`, `featured`, `description`, `image`, `rating`, `is_available`, `is_multi_branch`, `created_at`, `updated_at`) VALUES
(225, 3, NULL, 87, 'Sarees bpink', 32.20, NULL, 37, 'SKU3074', 1, 'High-quality Sarees bpink with excellent craftsmanship and attention to detail.', 'storage/products/Classic Sarees bpink.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:29'),
(227, 3, NULL, 61, 'Sarees', 24.29, NULL, 49, 'SKU2284', 0, 'High-quality Sarees with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Sarees red.jpg', 4.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(228, 2, NULL, 58, 'Scarves', 47.70, NULL, 42, 'SKU4153', 0, 'High-quality Scarves with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Scarves black.jpg', 4.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(229, 5, NULL, 74, 'Scarves', 71.26, NULL, 51, 'SKU0053', 0, 'High-quality Scarves with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Classic Scarves blue.jpg', 4.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(230, 4, NULL, 82, 'Scarves', 35.69, NULL, 29, 'SKU4043', 0, 'High-quality Scarves with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Scarves red.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(231, 4, NULL, 53, 'Scrunchies', 76.34, NULL, 32, 'SKU9593', 1, 'High-quality Scrunchies with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Scrunchies black.jpg', 4.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(232, 5, NULL, 46, 'Scrunchies', 58.85, NULL, 25, 'SKU3525', 0, 'High-quality Scrunchies with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Classic Scrunchies blue.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(233, 6, NULL, 69, 'Scrunchies', 56.96, NULL, 53, 'SKU5255', 0, 'High-quality Scrunchies with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Scrunchies red.jpg', 3.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(234, 3, NULL, 69, 'Serums', 58.09, NULL, 37, 'SKU9509', 0, 'High-quality Serums with excellent craftsmanship and attention to detail.', 'Classic Serums.jpg', 4.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(235, 5, NULL, 83, 'Shapewear', 73.32, NULL, 39, 'SKU5729', 0, 'High-quality Shapewear with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'storage/products/Classic Shapewear brown.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(236, 4, NULL, 39, 'Shapewear', 36.36, NULL, 27, 'SKU0669', 0, 'High-quality Shapewear with excellent craftsmanship and attention to detail.', 'Classic Shapewear.jpg', 3.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(237, 5, NULL, 100, 'Sleepwear likeblue', 29.84, NULL, 53, 'SKU2150', 0, 'High-quality Sleepwear likeblue with excellent craftsmanship and attention to detail.', 'Classic Sleepwear likeblue.jpg', 4.10, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(239, 2, NULL, 37, 'Sleepwear', 67.67, NULL, 28, 'SKU3368', 0, 'High-quality Sleepwear with excellent craftsmanship and attention to detail. Available in beautiful purple color.', 'storage/products/Classic Sleepwear purple.jpg', 3.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(240, 2, NULL, 102, 'Smartwatches', 54.63, NULL, 54, 'SKU9156', 0, 'High-quality Smartwatches with excellent craftsmanship and attention to detail.', 'storage/products/Classic Smartwatches.jpg', 4.00, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(241, 6, NULL, 94, 'Sterilizers', 75.50, NULL, 53, 'SKU6948', 1, 'High-quality Sterilizers with excellent craftsmanship and attention to detail.', 'storage/products/Classic Sterilizers.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(242, 1, NULL, 51, 'Stiletto Heels', 42.93, NULL, 72, 'SKU2101', 1, 'High-quality Stiletto Heels with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Stiletto Heels black.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(243, 3, NULL, 51, 'Stiletto Heels', 65.59, NULL, 25, 'SKU4640', 0, 'High-quality Stiletto Heels with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Classic Stiletto Heels gray.jpg', 3.70, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(244, 5, NULL, 51, 'Stiletto Heels', 67.02, NULL, 47, 'SKU5737', 0, 'High-quality Stiletto Heels with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Classic Stiletto Heels red.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(245, 6, NULL, 97, 'Strollers', 45.43, NULL, 20, 'SKU9160', 0, 'High-quality Strollers with excellent craftsmanship and attention to detail.', 'Classic Strollers.jpg', 4.20, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(246, 5, NULL, 57, 'Sunglasses', 28.45, NULL, 73, 'SKU0171', 1, 'High-quality Sunglasses with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'Classic Sunglasses black.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(247, 2, NULL, 57, 'Sunglasses', 60.94, NULL, 39, 'SKU9477', 0, 'High-quality Sunglasses with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Sunglasses blue.jpg', 4.80, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(248, 1, NULL, 57, 'Sunglasses', 48.81, NULL, 75, 'SKU1074', 0, 'High-quality Sunglasses with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Classic Sunglasses red.jpg', 4.60, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(249, 6, NULL, 73, 'Sunscreens', 70.13, NULL, 76, 'SKU4060', 0, 'High-quality Sunscreens with excellent craftsmanship and attention to detail.', 'Classic Sunscreens.jpg', 3.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(250, 1, NULL, 56, 'Tops (blouses, tunics)', 33.94, NULL, 24, 'SKU2485', 0, 'High-quality Tops (blouses, tunics) with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Classic Tops (blouses, tunics) black.jpg', 4.30, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(251, 6, NULL, 101, 'Tops (blouses, tunics)', 45.68, NULL, 51, 'SKU4639', 0, 'High-quality Tops (blouses, tunics) with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Classic Tops (blouses, tunics) red.jpg', 3.50, 1, 0, '2025-06-03 03:28:53', '2025-06-03 03:28:53'),
(252, 3, NULL, 52, 'Tops (blouses, tunics)', 30.67, NULL, 80, 'SKU8677', 0, 'High-quality Tops (blouses, tunics) with excellent craftsmanship and attention to detail. Available in beautiful violet color.', 'storage/products/Classic Tops (blouses, tunics) violet.jpg', 4.90, 1, 0, '2025-06-03 03:28:53', '2025-06-03 13:12:35'),
(253, 6, NULL, 60, 'Tote bags Cyan', 67.96, NULL, 62, 'SKU5363', 1, 'High-quality Tote bags Cyan with excellent craftsmanship and attention to detail. Available in beautiful cyan color.', 'Classic Tote bags Cyan.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(254, 2, NULL, 60, 'Tote bags', 32.96, NULL, 67, 'SKU4710', 0, 'High-quality Tote bags with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Classic Tote bags blue.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(255, 4, NULL, 60, 'Tote bags', 34.23, NULL, 42, 'SKU8271', 0, 'High-quality Tote bags with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'Classic Tote bags white.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(256, 1, NULL, 87, 'Cotton Everyday Hijab', 66.61, NULL, 25, 'SKU5280', 0, 'High-quality Cotton Everyday Hijab with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Cotton Everyday Hijab black.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(257, 4, NULL, 104, 'Cotton Everyday Hijab', 51.85, NULL, 64, 'SKU6471', 0, 'High-quality Cotton Everyday Hijab with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Cotton Everyday Hijab gray.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(258, 6, NULL, 67, 'Cotton Everyday Hijab', 31.83, NULL, 54, 'SKU6798', 0, 'High-quality Cotton Everyday Hijab with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Cotton Everyday Hijab white.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(259, 6, NULL, 59, 'Leather Handbag', 79.22, NULL, 55, 'SKU7300', 0, 'High-quality Leather Handbag with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'Leather Handbag brown.jpg', 4.40, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(260, 2, NULL, 59, 'Leather Handbag', 23.96, NULL, 37, 'SKU3625', 0, 'High-quality Leather Handbag with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Leather Handbag green.jpg', 3.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(261, 2, NULL, 59, 'Leather Handbag', 33.04, NULL, 64, 'SKU0787', 0, 'High-quality Leather Handbag with excellent craftsmanship and attention to detail. Available in beautiful pink color.', 'storage/products/Leather Handbag pink.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(262, 3, NULL, 59, 'Luxury Leather Handbag', 35.19, NULL, 79, 'SKU1977', 1, 'High-quality Luxury Leather Handbag with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'Luxury Leather Handbag black.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(263, 6, NULL, 59, 'Luxury Leather Handbag', 61.87, NULL, 49, 'SKU5711', 0, 'High-quality Luxury Leather Handbag with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'Luxury Leather Handbag brown.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(264, 3, NULL, 59, 'Luxury Leather Handbag lightyellow', 78.13, NULL, 51, 'SKU9989', 0, 'High-quality Luxury Leather Handbag lightyellow with excellent craftsmanship and attention to detail.', 'Luxury Leather Handbag lightyellow.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(265, 1, NULL, 39, 'Maxi Dress', 64.12, NULL, 75, 'SKU3958', 0, 'High-quality Maxi Dress with excellent craftsmanship and attention to detail. Available in beautiful cyan color.', 'storage/products/Maxi Dress cyan.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(266, 2, NULL, 39, 'Maxi Dress', 50.60, NULL, 69, 'SKU4091', 1, 'High-quality Maxi Dress with excellent craftsmanship and attention to detail. Available in beautiful pink color.', 'storage/products/Maxi Dress pink.jpg', 4.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(267, 3, NULL, 39, 'Maxi Dress', 21.89, NULL, 29, 'SKU9605', 0, 'High-quality Maxi Dress with excellent craftsmanship and attention to detail. Available in beautiful purple color.', 'Maxi Dress purple.jpg', 3.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(268, 5, NULL, 37, 'Activeweardark', 84.51, 109.86, 37, 'SKU2621', 0, 'High-quality Activeweardark with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Activewear bluedark.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(269, 2, NULL, 37, 'Activewear', 83.30, 108.28, 54, 'SKU0956', 0, 'High-quality Activewear with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Premium Activewear red.jpg', 3.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(270, 3, NULL, 85, 'Baby carriers', 107.18, 139.33, 44, 'SKU3850', 0, 'High-quality Baby carriers with excellent craftsmanship and attention to detail.', 'Premium Baby carriers.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(271, 6, NULL, 54, 'Belly support belts', 93.38, 121.39, 64, 'SKU2782', 0, 'High-quality Belly support belts with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Belly support belts black.jpg', 3.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(272, 4, NULL, 54, 'Belly support belts', 91.25, 118.62, 50, 'SKU5473', 1, 'High-quality Belly support belts with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Belly support belts blue.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(273, 2, NULL, 54, 'Belly support belts', 105.95, 137.73, 25, 'SKU7431', 0, 'High-quality Belly support belts with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Premium Belly support belts red.jpg', 4.40, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(274, 1, NULL, 54, 'Belts', 102.45, 133.19, 77, 'SKU0983', 1, 'High-quality Belts with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Belts black.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(275, 3, NULL, 54, 'Belts', 102.65, 133.44, 54, 'SKU9621', 0, 'High-quality Belts with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Belts blue.jpg', 4.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(276, 5, NULL, 56, 'Blushes', 103.13, 134.06, 31, 'SKU3062', 0, 'High-quality Blushes with excellent craftsmanship and attention to detail.', 'storage/products/Premium Blushes.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(277, 6, NULL, 61, 'Body mists', 40.04, 52.05, 47, 'SKU7591', 0, 'High-quality Body mists with excellent craftsmanship and attention to detail.', 'storage/products/Premium Body mists.jpg', 5.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(278, 3, NULL, 49, 'Boots', 64.58, 83.95, 34, 'SKU9220', 1, 'High-quality Boots with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Boots black.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(279, 3, NULL, 49, 'Boots', 68.36, 88.86, 63, 'SKU6079', 1, 'High-quality Boots with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'storage/products/Premium Boots brown.jpg', 4.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(280, 2, NULL, 49, 'Boots', 40.79, 53.02, 60, 'SKU3498', 0, 'High-quality Boots with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Premium Boots gray.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(281, 3, NULL, 98, 'Bottles', 40.16, 52.20, 67, 'SKU3335', 0, 'High-quality Bottles with excellent craftsmanship and attention to detail.', 'Premium Bottles.jpg', 4.30, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(282, 4, NULL, 63, 'Bottoms (jeans, skirts)', 62.73, 81.55, 58, 'SKU1198', 0, 'High-quality Bottoms (jeans, skirts) with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'Premium Bottoms (jeans, skirts) black.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(283, 6, NULL, 63, 'Bottoms (jeans, skirts)', 33.21, 43.17, 52, 'SKU6296', 0, 'High-quality Bottoms (jeans, skirts) with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Bottoms (jeans, skirts) blue.jpg', 4.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(285, 5, NULL, 62, 'Bracelets', 53.69, 69.79, 53, 'SKU2713', 0, 'High-quality Bracelets with excellent craftsmanship and attention to detail.', 'storage/products/Premium Bracelets.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(286, 3, NULL, 85, 'Bras', 95.39, 124.00, 62, 'SKU6336', 0, 'High-quality Bras with excellent craftsmanship and attention to detail.', 'Premium Bras.jpg', 4.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(287, 2, NULL, 87, 'Breast pumps', 95.64, 124.33, 48, 'SKU8689', 1, 'High-quality Breast pumps with excellent craftsmanship and attention to detail.', 'storage/products/Premium Breast pumps.jpg', 4.20, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(288, 5, NULL, 96, 'Car seats', 62.04, 80.65, 62, 'SKU6064', 0, 'High-quality Car seats with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Premium Car seats gray.jpg', 3.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(289, 4, NULL, 38, 'Cleansers', 49.94, 64.92, 56, 'SKU2044', 0, 'High-quality Cleansers with excellent craftsmanship and attention to detail.', 'storage/products/Premium Cleansers.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(290, 4, NULL, 38, 'Clips', 80.12, 104.15, 25, 'SKU2886', 0, 'High-quality Clips with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Clips black.jpg', 4.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(291, 3, NULL, 65, 'Clips', 84.29, 109.57, 67, 'SKU7757', 0, 'High-quality Clips with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Clips blue.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(292, 6, NULL, 98, 'Clips', 93.71, 121.82, 72, 'SKU1264', 1, 'High-quality Clips with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Premium Clips red.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(293, 1, NULL, 51, 'Conditioners', 60.23, 78.29, 22, 'SKU6184', 0, 'High-quality Conditioners with excellent craftsmanship and attention to detail.', 'storage/products/Premium Conditioners.jpg', 4.30, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(294, 1, NULL, 99, 'Deodorants', 119.84, 155.79, 51, 'SKU0789', 0, 'High-quality Deodorants with excellent craftsmanship and attention to detail.', 'storage/products/Premium Deodorants.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(295, 2, NULL, 103, 'Digital', 83.96, 109.14, 46, 'SKU3507', 0, 'High-quality Digital with excellent craftsmanship and attention to detail.', 'storage/products/Premium Digital.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(296, 6, NULL, 63, 'Earrings', 72.15, 93.80, 54, 'SKU0764', 0, 'High-quality Earrings with excellent craftsmanship and attention to detail.', 'storage/products/Premium Earrings.jpg', 4.20, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(297, 4, NULL, 82, 'Face masks', 113.07, 146.99, 49, 'SKU4524', 0, 'High-quality Face masks with excellent craftsmanship and attention to detail.', 'Premium Face masks.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(298, 6, NULL, 82, 'Hair oils', 76.37, 99.27, 35, 'SKU3890', 0, 'High-quality Hair oils with excellent craftsmanship and attention to detail.', 'storage/products/Premium Hair oils.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(299, 2, NULL, 57, 'Hairbands', 63.17, 82.11, 37, 'SKU9784', 0, 'High-quality Hairbands with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Hairbands black.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(300, 4, NULL, 99, 'Hairbands', 109.56, 142.43, 30, 'SKU3437', 0, 'High-quality Hairbands with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Hairbands blue.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(302, 1, NULL, 55, 'Hat', 46.40, 60.31, 56, 'SKU1366', 1, 'High-quality Hat with excellent craftsmanship and attention to detail.', 'storage/products/Premium Hat.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(303, 5, NULL, 41, 'High chairs', 61.62, 80.11, 24, 'SKU4785', 0, 'High-quality High chairs with excellent craftsmanship and attention to detail.', 'storage/products/Premium High chairs.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(304, 5, NULL, 38, 'Kaftans', 91.02, 118.33, 54, 'SKU9917', 0, 'High-quality Kaftans with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Kaftans black.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(305, 3, NULL, 89, 'Kaftans', 68.03, 88.43, 78, 'SKU2048', 0, 'High-quality Kaftans with excellent craftsmanship and attention to detail. Available in beautiful gold color.', 'Premium Kaftans gold.jpg', 4.30, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(307, 4, NULL, 77, 'Kaftans', 50.36, 65.46, 77, 'SKU1836', 0, 'High-quality Kaftans with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Premium Kaftans red.jpg', 4.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(308, 5, NULL, 86, 'Lingerie', 75.95, 98.73, 27, 'SKU7009', 0, 'High-quality Lingerie with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Lingerie black.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(309, 3, NULL, 86, 'Lingerie', 95.55, 124.22, 25, 'SKU3476', 0, 'High-quality Lingerie with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Lingerie blue.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(310, 4, NULL, 86, 'Lingerie', 40.53, 52.69, 51, 'SKU3516', 0, 'High-quality Lingerie with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Premium Lingerie red.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(311, 4, NULL, 69, 'Lipsticks', 80.04, 104.05, 39, 'SKU6003', 0, 'High-quality Lipsticks with excellent craftsmanship and attention to detail.', 'Premium Lipsticks.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(312, 4, NULL, 71, 'Loungewear darkred', 97.23, 126.40, 47, 'SKU9571', 0, 'High-quality Loungewear darkred with excellent craftsmanship and attention to detail.', 'Premium Loungewear darkred.jpg', 4.40, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(313, 1, NULL, 63, 'Loungewear', 82.95, 107.84, 30, 'SKU3375', 0, 'High-quality Loungewear with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Premium Loungewear gray.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(314, 5, NULL, 50, 'Loungewear', 93.57, 121.64, 62, 'SKU2877', 0, 'High-quality Loungewear with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Premium Loungewear white.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(315, 3, NULL, 68, 'Mascaras', 40.25, 52.32, 51, 'SKU4628', 0, 'High-quality Mascaras with excellent craftsmanship and attention to detail.', 'Premium Mascaras.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(316, 2, NULL, 77, 'Maternity clothing', 67.70, 88.00, 49, 'SKU3259', 0, 'High-quality Maternity clothing with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Maternity clothing black.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(317, 2, NULL, 53, 'Maternity clothing', 98.36, 127.86, 46, 'SKU3578', 0, 'High-quality Maternity clothing with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'storage/products/Premium Maternity clothing brown.jpg', 4.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(318, 5, NULL, 69, 'Maternity clothing dark', 68.01, 88.41, 26, 'SKU8230', 0, 'High-quality Maternity clothing dark with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Premium Maternity clothing green dark.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(319, 3, NULL, 91, 'Maternity wear', 95.45, 124.08, 26, 'SKU6627', 0, 'High-quality Maternity wear with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'Premium Maternity wear brown.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(320, 3, NULL, 100, 'Maternity wear', 38.12, 49.55, 77, 'SKU7946', 1, 'High-quality Maternity wear with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Premium Maternity wear green.jpg', 5.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(321, 2, NULL, 37, 'Maternity wear', 79.41, 103.23, 52, 'SKU6310', 0, 'High-quality Maternity wear with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'storage/products/Premium Maternity wear orange.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(322, 5, NULL, 65, 'Moisturizers', 92.37, 120.08, 44, 'SKU9847', 0, 'High-quality Moisturizers with excellent craftsmanship and attention to detail.', 'storage/products/Premium Moisturizers.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(323, 1, NULL, 64, 'Necklaces', 99.47, 129.30, 51, 'SKU9694', 0, 'High-quality Necklaces with excellent craftsmanship and attention to detail.', 'storage/products/Premium Necklaces.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(324, 5, NULL, 85, 'Nursing bras', 109.34, 142.14, 44, 'SKU0518', 0, 'High-quality Nursing bras with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Nursing bras black.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(325, 4, NULL, 85, 'Nursing bras', 67.94, 88.32, 40, 'SKU2915', 0, 'High-quality Nursing bras with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Nursing bras blue.jpg', 4.30, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(326, 4, NULL, 85, 'Nursing bras', 108.59, 141.16, 28, 'SKU0994', 0, 'High-quality Nursing bras with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Premium Nursing bras red.jpg', 3.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(327, 3, NULL, 92, 'Onesies', 84.57, 109.94, 46, 'SKU6138', 1, 'High-quality Onesies with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'Premium Onesies orange.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(328, 6, NULL, 92, 'Onesies', 38.69, 50.29, 42, 'SKU8725', 0, 'High-quality Onesies with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Premium Onesies white.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(329, 6, NULL, 92, 'Onesies', 94.85, 123.30, 54, 'SKU4080', 0, 'High-quality Onesies with excellent craftsmanship and attention to detail. Available in beautiful yellow color.', 'storage/products/Premium Onesies yellow.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(330, 1, NULL, 101, 'Outerwear (jackets, coats)', 44.73, 58.15, 69, 'SKU2629', 0, 'High-quality Outerwear (jackets, coats) with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Outerwear (jackets, coats) black.jpg', 3.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(331, 4, NULL, 76, 'Outerwear (jackets, coats)', 34.16, 44.40, 76, 'SKU2926', 0, 'High-quality Outerwear (jackets, coats) with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Outerwear (jackets, coats) blue.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(332, 5, NULL, 90, 'Outerwear (jackets, coats) darkred', 118.31, 153.80, 78, 'SKU7142', 0, 'High-quality Outerwear (jackets, coats) darkred with excellent craftsmanship and attention to detail.', 'storage/products/Premium Outerwear (jackets, coats) darkred.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(333, 5, NULL, 39, 'Outerwear', 40.97, 53.25, 24, 'SKU4942', 1, 'High-quality Outerwear with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Outerwear black.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(334, 4, NULL, 85, 'Outerwear light', 63.78, 82.91, 22, 'SKU4997', 0, 'High-quality Outerwear light with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Premium Outerwear light green.jpg', 4.20, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(335, 5, NULL, 83, 'Outerwearcoo', 36.96, 48.05, 29, 'SKU6171', 0, 'High-quality Outerwearcoo with excellent craftsmanship and attention to detail. Available in beautiful yellow color.', 'storage/products/Premium Outerwear yellowcoo.jpg', 4.40, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(336, 4, NULL, 87, 'Panties', 36.98, 48.07, 71, 'SKU4238', 0, 'High-quality Panties with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'Premium Panties black.jpg', 4.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(337, 5, NULL, 87, 'Panties', 102.42, 133.15, 35, 'SKU7313', 0, 'High-quality Panties with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Panties blue.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(338, 5, NULL, 87, 'Panties', 32.69, 42.49, 61, 'SKU7983', 1, 'High-quality Panties with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Premium Panties red.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(339, 3, NULL, 95, 'Pray Clothes', 77.63, 100.91, 42, 'SKU3298', 0, 'High-quality Pray Clothes with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'Premium Pray Clothes white.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(340, 4, NULL, 47, 'Pray Clothes', 53.21, 69.17, 64, 'SKU4433', 1, 'High-quality Pray Clothes with excellent craftsmanship and attention to detail.', 'Premium Pray Clothes.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(341, 6, NULL, 65, 'Rings', 47.79, 62.13, 56, 'SKU6657', 0, 'High-quality Rings with excellent craftsmanship and attention to detail.', 'storage/products/Premium Rings.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(342, 2, NULL, 104, 'Salwar Kameez', 71.87, 93.42, 28, 'SKU3715', 0, 'High-quality Salwar Kameez with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Salwar Kameez black.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(343, 5, NULL, 42, 'Salwar Kameezgray', 94.22, 122.48, 76, 'SKU9017', 0, 'High-quality Salwar Kameezgray with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Salwar Kameez bluegray.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(344, 3, NULL, 102, 'Salwar Kameez', 47.85, 62.21, 21, 'SKU6020', 1, 'High-quality Salwar Kameez with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Premium Salwar Kameez red.jpg', 4.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(345, 4, NULL, 52, 'Sandals', 46.28, 60.16, 53, 'SKU8463', 0, 'High-quality Sandals with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Sandals black.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(346, 5, NULL, 52, 'Sandals', 91.62, 119.11, 31, 'SKU3532', 0, 'High-quality Sandals with excellent craftsmanship and attention to detail. Available in beautiful green color.', 'storage/products/Premium Sandals green.jpg', 3.90, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(347, 2, NULL, 52, 'Sandals', 72.18, 93.83, 33, 'SKU7807', 0, 'High-quality Sandals with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Premium Sandals white.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:35'),
(349, 6, NULL, 60, 'Sarees', 94.41, 122.73, 35, 'SKU9347', 0, 'High-quality Sarees with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Sarees blue.jpg', 4.30, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(350, 1, NULL, 93, 'Sarees', 116.45, 151.38, 79, 'SKU3099', 0, 'High-quality Sarees with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'storage/products/Premium Sarees red.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(351, 3, NULL, 70, 'Scarves', 93.93, 122.11, 67, 'SKU3303', 1, 'High-quality Scarves with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Scarves blue.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(352, 5, NULL, 52, 'Scarves', 107.67, 139.97, 73, 'SKU2198', 1, 'High-quality Scarves with excellent craftsmanship and attention to detail. Available in beautiful brown color.', 'storage/products/Premium Scarves brown.jpg', 3.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(353, 6, NULL, 101, 'Scarves darkred', 37.29, 48.48, 69, 'SKU3441', 0, 'High-quality Scarves darkred with excellent craftsmanship and attention to detail.', 'Premium Scarves darkred.jpg', 4.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(354, 5, NULL, 100, 'Scrunchies', 76.37, 99.27, 49, 'SKU9284', 0, 'High-quality Scrunchies with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'Premium Scrunchies black.jpg', 4.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(355, 6, NULL, 81, 'Scrunchies', 94.70, 123.10, 40, 'SKU7952', 0, 'High-quality Scrunchies with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Premium Scrunchies blue.jpg', 4.40, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(356, 5, NULL, 99, 'Scrunchies', 66.48, 86.42, 30, 'SKU5472', 0, 'High-quality Scrunchies with excellent craftsmanship and attention to detail. Available in beautiful red color.', 'Premium Scrunchies red.jpg', 4.30, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(357, 4, NULL, 43, 'Serums', 35.37, 45.98, 77, 'SKU6362', 1, 'High-quality Serums with excellent craftsmanship and attention to detail.', 'storage/products/Premium Serums.jpg', 4.10, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(358, 4, NULL, 37, 'Silk Hijab', 100.65, 130.85, 37, 'SKU4092', 0, 'High-quality Silk Hijab with excellent craftsmanship and attention to detail.', 'storage/products/Premium Silk Hijab.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(359, 3, NULL, 75, 'Sleepwear', 57.14, 74.28, 24, 'SKU4203', 0, 'High-quality Sleepwear with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'Premium Sleepwear black.jpg', 4.40, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(360, 4, NULL, 65, 'Sleepwear', 32.33, 42.02, 20, 'SKU1828', 0, 'High-quality Sleepwear with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Premium Sleepwear gray.jpg', 5.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(361, 6, NULL, 72, 'Sleepwear', 73.68, 95.78, 79, 'SKU3696', 0, 'High-quality Sleepwear with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'Premium Sleepwear white.jpg', 4.70, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(362, 5, NULL, 102, 'Smartwatches', 50.16, 65.21, 35, 'SKU7725', 0, 'High-quality Smartwatches with excellent craftsmanship and attention to detail.', 'storage/products/Premium Smartwatches.jpg', 5.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(363, 4, NULL, 37, 'Sterilizers', 113.28, 147.26, 20, 'SKU7640', 0, 'High-quality Sterilizers with excellent craftsmanship and attention to detail.', 'storage/products/Premium Sterilizers.jpg', 3.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(364, 6, NULL, 97, 'Strollers', 62.34, 81.04, 44, 'SKU7895', 0, 'High-quality Strollers with excellent craftsmanship and attention to detail.', 'Premium Strollers.jpg', 3.80, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(365, 1, NULL, 57, 'Sunglasses', 101.43, 131.86, 32, 'SKU1800', 0, 'High-quality Sunglasses with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Premium Sunglasses black.jpg', 4.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(366, 1, NULL, 57, 'Sunglasses', 75.18, 97.73, 54, 'SKU5960', 1, 'High-quality Sunglasses with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Sunglasses blue.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(367, 5, NULL, 57, 'Sunglasses', 115.55, 150.21, 22, 'SKU2017', 0, 'High-quality Sunglasses with excellent craftsmanship and attention to detail. Available in beautiful pink color.', 'storage/products/Premium Sunglasses pink.jpg', 5.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(368, 1, NULL, 59, 'Tops (blouses, tunics)', 114.45, 148.79, 76, 'SKU9314', 0, 'High-quality Tops (blouses, tunics) with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Tops (blouses, tunics) blue.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(369, 1, NULL, 69, 'Tops (blouses, tunics)', 51.87, 67.43, 80, 'SKU2019', 0, 'High-quality Tops (blouses, tunics) with excellent craftsmanship and attention to detail. Available in beautiful orange color.', 'storage/products/Premium Tops (blouses, tunics) orange.jpg', 4.20, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(370, 2, NULL, 60, 'Tote bags', 40.49, 52.63, 37, 'SKU5716', 0, 'High-quality Tote bags with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Tote bags blue.jpg', 4.50, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(371, 2, NULL, 60, 'Tote bags light', 80.04, 104.05, 25, 'SKU3257', 0, 'High-quality Tote bags light with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'storage/products/Premium Tote bags light blue.jpg', 4.00, 1, 0, '2025-06-03 03:28:54', '2025-06-03 13:12:43'),
(372, 3, NULL, 60, 'Tote bags', 107.31, 139.50, 52, 'SKU9555', 0, 'High-quality Tote bags with excellent craftsmanship and attention to detail. Available in beautiful violet color.', 'Premium Tote bags violet.jpg', 4.20, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(373, 6, NULL, 56, 'Rottia Blue', 52.90, NULL, 40, 'SKU4368', 0, 'High-quality Rottia Blue with excellent craftsmanship and attention to detail. Available in beautiful blue color.', 'Rottia Blue.jpg', 3.60, 1, 0, '2025-06-03 03:28:54', '2025-06-03 03:28:54'),
(374, 4, NULL, 82, 'Rottia Violet', 43.19, NULL, 28, 'SKU9054', 0, 'High-quality Rottia Violet with excellent craftsmanship and attention to detail. Available in beautiful violet color.', 'storage/products/Rottia Violet.jpg', 3.90, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43'),
(375, 4, NULL, 53, 'Running Sneakers', 74.29, NULL, 41, 'SKU9406', 0, 'High-quality Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/Running Sneakers black.jpg', 4.60, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43'),
(376, 1, NULL, 53, 'Running Sneakers', 53.53, NULL, 73, 'SKU1193', 0, 'High-quality Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful gray color.', 'storage/products/Running Sneakers gray.jpg', 4.80, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43'),
(377, 3, NULL, 53, 'Running Sneakers', 39.99, NULL, 79, 'SKU7332', 0, 'High-quality Running Sneakers with excellent craftsmanship and attention to detail. Available in beautiful white color.', 'storage/products/Running Sneakers white.jpg', 3.60, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43'),
(378, 6, NULL, 102, 'analog', 77.85, 101.21, 78, 'SKU8878', 0, 'High-quality analog with excellent craftsmanship and attention to detail.', 'storage/products/premium analog.jpg', 4.10, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43'),
(379, 5, NULL, 58, 'sic Backpacks', 20.84, NULL, 65, 'SKU9386', 0, 'High-quality sic Backpacks with excellent craftsmanship and attention to detail. Available in beautiful black color.', 'storage/products/sic Backpacks black.jpg', 4.70, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43'),
(380, 5, NULL, 62, 'Bracelets', 107.84, 140.19, 61, 'SKU1934', 0, 'High-quality Bracelets with excellent craftsmanship and attention to detail.', 'storage/products/Premium Bracelets.jpeg', 4.50, 1, 0, '2025-06-03 03:28:55', '2025-06-03 13:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_branches`
--

CREATE TABLE `product_branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `color_code` varchar(10) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `name`, `color_code`, `image`, `price_adjustment`, `stock`, `display_order`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 38, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 29, 0, 1, '2025-06-03 03:24:36', '2025-06-03 03:24:36'),
(2, 38, 'Red', '#FF0000', '/images/placeholder.jpg', 5.00, 15, 1, 0, '2025-06-03 03:24:37', '2025-06-03 03:24:37'),
(3, 38, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 28, 2, 0, '2025-06-03 03:24:38', '2025-06-03 03:24:38'),
(4, 38, 'White', '#FFFFFF', '/images/placeholder.jpg', 0.00, 23, 3, 0, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(5, 39, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 25, 0, 1, '2025-06-03 03:24:42', '2025-06-03 03:24:42'),
(6, 39, 'Red', '#FF0000', '/images/placeholder.jpg', 5.00, 25, 1, 0, '2025-06-03 03:24:43', '2025-06-03 03:24:43'),
(7, 39, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 21, 2, 0, '2025-06-03 03:24:45', '2025-06-03 03:24:45'),
(8, 39, 'White', '#FFFFFF', '/images/placeholder.jpg', 0.00, 16, 3, 0, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(9, 42, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 18, 0, 1, '2025-06-03 03:24:51', '2025-06-03 03:24:51'),
(10, 42, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 16, 1, 0, '2025-06-03 03:24:53', '2025-06-03 03:24:53'),
(11, 42, 'Red', '#FF0000', '/images/placeholder.jpg', 5.00, 17, 2, 0, '2025-06-03 03:24:54', '2025-06-03 03:24:54'),
(12, 43, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 16, 0, 1, '2025-06-03 03:24:57', '2025-06-03 03:24:57'),
(13, 43, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 14, 1, 0, '2025-06-03 03:24:58', '2025-06-03 03:24:58'),
(14, 43, 'Red', '#FF0000', '/images/placeholder.jpg', 5.00, 26, 2, 0, '2025-06-03 03:24:59', '2025-06-03 03:24:59'),
(15, 48, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 15, 0, 1, '2025-06-03 03:25:06', '2025-06-03 03:25:06'),
(16, 48, 'Navy Blue', '#000080', '/images/placeholder.jpg', 0.00, 28, 1, 0, '2025-06-03 03:25:07', '2025-06-03 03:25:07'),
(17, 48, 'Dark Brown', '#654321', '/images/placeholder.jpg', 0.00, 13, 2, 0, '2025-06-03 03:25:08', '2025-06-03 03:25:08'),
(18, 49, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 14, 0, 1, '2025-06-03 03:25:11', '2025-06-03 03:25:11'),
(19, 49, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 20, 1, 0, '2025-06-03 03:25:12', '2025-06-03 03:25:12'),
(20, 49, 'Red', '#FF0000', '/images/placeholder.jpg', 5.00, 21, 2, 0, '2025-06-03 03:25:13', '2025-06-03 03:25:13'),
(21, 50, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 21, 0, 1, '2025-06-03 03:25:16', '2025-06-03 03:25:16'),
(22, 50, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 13, 1, 0, '2025-06-03 03:25:17', '2025-06-03 03:25:17'),
(23, 50, 'Red', '#FF0000', '/images/placeholder.jpg', 5.00, 23, 2, 0, '2025-06-03 03:25:18', '2025-06-03 03:25:18'),
(24, 51, 'Black', '#000000', '/images/placeholder.jpg', 0.00, 24, 0, 1, '2025-06-03 03:25:22', '2025-06-03 03:25:22'),
(25, 51, 'Blue', '#0000FF', '/images/placeholder.jpg', 0.00, 25, 1, 0, '2025-06-03 03:25:23', '2025-06-03 03:25:23');

-- --------------------------------------------------------

--
-- Table structure for table `product_color_sizes`
--

CREATE TABLE `product_color_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_color_id` bigint(20) UNSIGNED NOT NULL,
  `product_size_id` bigint(20) UNSIGNED NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_color_sizes`
--

INSERT INTO `product_color_sizes` (`id`, `product_id`, `product_color_id`, `product_size_id`, `stock`, `price_adjustment`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 38, 1, 1, 6, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(2, 38, 1, 2, 7, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(3, 38, 1, 3, 6, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(4, 38, 1, 4, 6, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(5, 38, 1, 5, 3, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(6, 38, 1, 6, 9, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(7, 38, 2, 1, 8, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(8, 38, 2, 2, 4, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(9, 38, 2, 3, 7, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(10, 38, 2, 4, 7, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(11, 38, 2, 5, 2, 10.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(12, 38, 2, 6, 3, 10.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(13, 38, 3, 1, 7, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(14, 38, 3, 2, 5, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(15, 38, 3, 3, 2, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(16, 38, 3, 4, 8, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(17, 38, 3, 5, 6, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(18, 38, 3, 6, 9, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(19, 38, 4, 1, 4, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(20, 38, 4, 2, 5, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(21, 38, 4, 3, 5, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(22, 38, 4, 4, 8, 0.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(23, 38, 4, 5, 7, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(24, 38, 4, 6, 3, 5.00, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(25, 39, 5, 7, 9, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(26, 39, 5, 8, 6, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(27, 39, 5, 9, 4, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(28, 39, 5, 10, 2, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(29, 39, 5, 11, 4, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(30, 39, 5, 12, 5, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(31, 39, 6, 7, 2, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(32, 39, 6, 8, 6, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(33, 39, 6, 9, 6, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(34, 39, 6, 10, 4, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(35, 39, 6, 11, 10, 10.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(36, 39, 6, 12, 10, 10.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(37, 39, 7, 7, 10, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(38, 39, 7, 8, 2, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(39, 39, 7, 9, 10, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(40, 39, 7, 10, 7, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(41, 39, 7, 11, 4, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(42, 39, 7, 12, 3, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(43, 39, 8, 7, 9, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(44, 39, 8, 8, 2, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(45, 39, 8, 9, 7, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(46, 39, 8, 10, 3, 0.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(47, 39, 8, 11, 8, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(48, 39, 8, 12, 8, 5.00, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(49, 48, 15, 13, 2, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(50, 48, 15, 14, 8, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(51, 48, 15, 15, 7, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(52, 48, 15, 16, 10, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(53, 48, 15, 17, 9, 5.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(54, 48, 15, 18, 10, 5.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(55, 48, 16, 13, 4, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(56, 48, 16, 14, 4, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(57, 48, 16, 15, 9, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(58, 48, 16, 16, 8, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(59, 48, 16, 17, 7, 5.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(60, 48, 16, 18, 6, 5.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(61, 48, 17, 13, 5, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(62, 48, 17, 14, 5, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(63, 48, 17, 15, 4, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(64, 48, 17, 16, 5, 0.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(65, 48, 17, 17, 7, 5.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(66, 48, 17, 18, 7, 5.00, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `product_option_types`
--

CREATE TABLE `product_option_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'select',
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_option_values`
--

CREATE TABLE `product_option_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `option_type_id` bigint(20) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `size_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `standardized_size_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `additional_info` varchar(255) DEFAULT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size_category_id`, `standardized_size_id`, `name`, `value`, `additional_info`, `price_adjustment`, `stock`, `display_order`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 38, 1, 1, 'XXS', 'Extra Extra Small', NULL, 0.00, 10, 0, 0, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(2, 38, 1, 2, 'XS', 'Extra Small', NULL, 0.00, 17, 1, 0, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(3, 38, 1, 3, 'S', 'Small', NULL, 0.00, 17, 2, 1, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(4, 38, 1, 4, 'M', 'Medium', NULL, 0.00, 16, 3, 0, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(5, 38, 1, 5, 'L', 'Large', NULL, 5.00, 9, 4, 0, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(6, 38, 1, 6, 'XL', 'Extra Large', NULL, 5.00, 17, 5, 0, '2025-06-03 03:24:39', '2025-06-03 03:24:39'),
(7, 39, 1, 1, 'XXS', 'Extra Extra Small', NULL, 0.00, 7, 0, 0, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(8, 39, 1, 2, 'XS', 'Extra Small', NULL, 0.00, 9, 1, 0, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(9, 39, 1, 3, 'S', 'Small', NULL, 0.00, 13, 2, 1, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(10, 39, 1, 4, 'M', 'Medium', NULL, 0.00, 13, 3, 0, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(11, 39, 1, 5, 'L', 'Large', NULL, 5.00, 5, 4, 0, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(12, 39, 1, 6, 'XL', 'Extra Large', NULL, 5.00, 12, 5, 0, '2025-06-03 03:24:46', '2025-06-03 03:24:46'),
(13, 48, 1, 1, 'XXS', 'Extra Extra Small', NULL, 0.00, 13, 0, 0, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(14, 48, 1, 2, 'XS', 'Extra Small', NULL, 0.00, 10, 1, 0, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(15, 48, 1, 3, 'S', 'Small', NULL, 0.00, 19, 2, 1, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(16, 48, 1, 4, 'M', 'Medium', NULL, 0.00, 19, 3, 0, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(17, 48, 1, 5, 'L', 'Large', NULL, 5.00, 11, 4, 0, '2025-06-03 03:25:09', '2025-06-03 03:25:09'),
(18, 48, 1, 6, 'XL', 'Extra Large', NULL, 5.00, 16, 5, 0, '2025-06-03 03:25:09', '2025-06-03 03:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `product_specifications`
--

CREATE TABLE `product_specifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_specifications`
--

INSERT INTO `product_specifications` (`id`, `product_id`, `key`, `value`, `display_order`, `created_at`, `updated_at`) VALUES
(113, 34, 'Brand', 'Premium Brand', 0, '2025-06-03 03:24:29', '2025-06-03 03:24:29'),
(114, 34, 'Quality', 'High', 1, '2025-06-03 03:24:29', '2025-06-03 03:24:29'),
(115, 34, 'Warranty', '1 Year', 2, '2025-06-03 03:24:29', '2025-06-03 03:24:29'),
(116, 34, 'Origin', 'Imported', 3, '2025-06-03 03:24:29', '2025-06-03 03:24:29'),
(117, 35, 'Brand', 'Classic Brand', 0, '2025-06-03 03:24:30', '2025-06-03 03:24:30'),
(118, 35, 'Style', 'Traditional', 1, '2025-06-03 03:24:30', '2025-06-03 03:24:30'),
(119, 35, 'Quality', 'Standard', 2, '2025-06-03 03:24:30', '2025-06-03 03:24:30'),
(120, 35, 'Origin', 'Local', 3, '2025-06-03 03:24:30', '2025-06-03 03:24:30'),
(121, 36, 'Brand', 'Premium Brand', 0, '2025-06-03 03:24:31', '2025-06-03 03:24:31'),
(122, 36, 'Quality', 'High', 1, '2025-06-03 03:24:32', '2025-06-03 03:24:32'),
(123, 36, 'Warranty', '1 Year', 2, '2025-06-03 03:24:32', '2025-06-03 03:24:32'),
(124, 36, 'Origin', 'Imported', 3, '2025-06-03 03:24:32', '2025-06-03 03:24:32'),
(125, 37, 'Brand', 'Classic Brand', 0, '2025-06-03 03:24:33', '2025-06-03 03:24:33'),
(126, 37, 'Style', 'Traditional', 1, '2025-06-03 03:24:33', '2025-06-03 03:24:33'),
(127, 37, 'Quality', 'Standard', 2, '2025-06-03 03:24:33', '2025-06-03 03:24:33'),
(128, 37, 'Origin', 'Local', 3, '2025-06-03 03:24:33', '2025-06-03 03:24:33'),
(129, 38, 'Material', 'Polyester blend', 0, '2025-06-03 03:24:34', '2025-06-03 03:24:34'),
(130, 38, 'Length', 'Maxi', 1, '2025-06-03 03:24:34', '2025-06-03 03:24:34'),
(131, 38, 'Sleeve Type', 'Long sleeve', 2, '2025-06-03 03:24:34', '2025-06-03 03:24:34'),
(132, 38, 'Occasion', 'Formal', 3, '2025-06-03 03:24:34', '2025-06-03 03:24:34'),
(133, 39, 'Material', 'Cotton', 0, '2025-06-03 03:24:41', '2025-06-03 03:24:41'),
(134, 39, 'Length', 'Knee-length', 1, '2025-06-03 03:24:41', '2025-06-03 03:24:41'),
(135, 39, 'Sleeve Type', 'Sleeveless', 2, '2025-06-03 03:24:41', '2025-06-03 03:24:41'),
(136, 39, 'Occasion', 'Casual', 3, '2025-06-03 03:24:41', '2025-06-03 03:24:41'),
(137, 40, 'Brand', 'Premium Brand', 0, '2025-06-03 03:24:47', '2025-06-03 03:24:47'),
(138, 40, 'Quality', 'High', 1, '2025-06-03 03:24:47', '2025-06-03 03:24:47'),
(139, 40, 'Warranty', '1 Year', 2, '2025-06-03 03:24:47', '2025-06-03 03:24:47'),
(140, 40, 'Origin', 'Imported', 3, '2025-06-03 03:24:47', '2025-06-03 03:24:47'),
(141, 41, 'Brand', 'Classic Brand', 0, '2025-06-03 03:24:49', '2025-06-03 03:24:49'),
(142, 41, 'Style', 'Traditional', 1, '2025-06-03 03:24:49', '2025-06-03 03:24:49'),
(143, 41, 'Quality', 'Standard', 2, '2025-06-03 03:24:49', '2025-06-03 03:24:49'),
(144, 41, 'Origin', 'Local', 3, '2025-06-03 03:24:49', '2025-06-03 03:24:49'),
(145, 42, 'Brand', 'Premium Brand', 0, '2025-06-03 03:24:50', '2025-06-03 03:24:50'),
(146, 42, 'Quality', 'High', 1, '2025-06-03 03:24:50', '2025-06-03 03:24:50'),
(147, 42, 'Warranty', '1 Year', 2, '2025-06-03 03:24:50', '2025-06-03 03:24:50'),
(148, 42, 'Origin', 'Imported', 3, '2025-06-03 03:24:50', '2025-06-03 03:24:50'),
(149, 43, 'Brand', 'Classic Brand', 0, '2025-06-03 03:24:55', '2025-06-03 03:24:55'),
(150, 43, 'Style', 'Traditional', 1, '2025-06-03 03:24:55', '2025-06-03 03:24:55'),
(151, 43, 'Quality', 'Standard', 2, '2025-06-03 03:24:55', '2025-06-03 03:24:55'),
(152, 43, 'Origin', 'Local', 3, '2025-06-03 03:24:55', '2025-06-03 03:24:55'),
(153, 44, 'Brand', 'Premium Brand', 0, '2025-06-03 03:25:00', '2025-06-03 03:25:00'),
(154, 44, 'Quality', 'High', 1, '2025-06-03 03:25:00', '2025-06-03 03:25:00'),
(155, 44, 'Warranty', '1 Year', 2, '2025-06-03 03:25:00', '2025-06-03 03:25:00'),
(156, 44, 'Origin', 'Imported', 3, '2025-06-03 03:25:00', '2025-06-03 03:25:00'),
(157, 45, 'Brand', 'Classic Brand', 0, '2025-06-03 03:25:01', '2025-06-03 03:25:01'),
(158, 45, 'Style', 'Traditional', 1, '2025-06-03 03:25:01', '2025-06-03 03:25:01'),
(159, 45, 'Quality', 'Standard', 2, '2025-06-03 03:25:01', '2025-06-03 03:25:01'),
(160, 45, 'Origin', 'Local', 3, '2025-06-03 03:25:01', '2025-06-03 03:25:01'),
(161, 46, 'Brand', 'Premium Brand', 0, '2025-06-03 03:25:02', '2025-06-03 03:25:02'),
(162, 46, 'Quality', 'High', 1, '2025-06-03 03:25:02', '2025-06-03 03:25:02'),
(163, 46, 'Warranty', '1 Year', 2, '2025-06-03 03:25:02', '2025-06-03 03:25:02'),
(164, 46, 'Origin', 'Imported', 3, '2025-06-03 03:25:02', '2025-06-03 03:25:02'),
(165, 47, 'Brand', 'Classic Brand', 0, '2025-06-03 03:25:04', '2025-06-03 03:25:04'),
(166, 47, 'Style', 'Traditional', 1, '2025-06-03 03:25:04', '2025-06-03 03:25:04'),
(167, 47, 'Quality', 'Standard', 2, '2025-06-03 03:25:04', '2025-06-03 03:25:04'),
(168, 47, 'Origin', 'Local', 3, '2025-06-03 03:25:04', '2025-06-03 03:25:04'),
(169, 48, 'Material', 'Crepe', 0, '2025-06-03 03:25:05', '2025-06-03 03:25:05'),
(170, 48, 'Style', 'Traditional', 1, '2025-06-03 03:25:05', '2025-06-03 03:25:05'),
(171, 48, 'Embellishment', 'Hand embroidery', 2, '2025-06-03 03:25:05', '2025-06-03 03:25:05'),
(172, 48, 'Origin', 'UAE', 3, '2025-06-03 03:25:05', '2025-06-03 03:25:05'),
(173, 49, 'Brand', 'Premium Brand', 0, '2025-06-03 03:25:10', '2025-06-03 03:25:10'),
(174, 49, 'Quality', 'High', 1, '2025-06-03 03:25:10', '2025-06-03 03:25:10'),
(175, 49, 'Warranty', '1 Year', 2, '2025-06-03 03:25:10', '2025-06-03 03:25:10'),
(176, 49, 'Origin', 'Imported', 3, '2025-06-03 03:25:10', '2025-06-03 03:25:10'),
(177, 50, 'Brand', 'Classic Brand', 0, '2025-06-03 03:25:15', '2025-06-03 03:25:15'),
(178, 50, 'Style', 'Traditional', 1, '2025-06-03 03:25:15', '2025-06-03 03:25:15'),
(179, 50, 'Quality', 'Standard', 2, '2025-06-03 03:25:15', '2025-06-03 03:25:15'),
(180, 50, 'Origin', 'Local', 3, '2025-06-03 03:25:15', '2025-06-03 03:25:15'),
(181, 51, 'Brand', 'Premium Brand', 0, '2025-06-03 03:25:19', '2025-06-03 03:25:19'),
(182, 51, 'Quality', 'High', 1, '2025-06-03 03:25:19', '2025-06-03 03:25:19'),
(183, 51, 'Warranty', '1 Year', 2, '2025-06-03 03:25:19', '2025-06-03 03:25:19'),
(184, 51, 'Origin', 'Imported', 3, '2025-06-03 03:25:19', '2025-06-03 03:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_ratings` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `provider_score` int(11) NOT NULL DEFAULT 0,
  `last_score_calculation` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `user_id`, `business_name`, `business_type`, `registration_number`, `description`, `address`, `city`, `state`, `postal_code`, `country`, `website`, `logo`, `status`, `is_verified`, `average_rating`, `total_ratings`, `view_count`, `order_count`, `provider_score`, `last_score_calculation`, `created_at`, `updated_at`) VALUES
(3, 12, 'GoodHijab', 'Hjabs', '29156916956', NULL, 'Sharjah - United Arab Emirates - Sharjah - United Arab Emirates', NULL, '--- N/A ---', NULL, 'United Arab Emirates', NULL, NULL, 'active', 0, 3.00, 2, 0, 0, 0, NULL, '2025-05-24 13:08:14', '2025-05-25 17:43:35'),
(4, 13, 'Fawaz Mask', 'Mask and Saona', '29156916944', NULL, 'Sharjah - United Arab Emirates - Sharjah - United Arab Emirates', NULL, '--- N/A ---', NULL, 'United Arab Emirates', NULL, NULL, 'active', 1, 3.00, 1, 0, 0, 0, NULL, '2025-05-24 13:13:30', '2025-05-25 13:13:16'),
(5, 14, 'Sample Provider Business', 'Food & Beverages', 'REG123456', 'A sample provider business for testing purposes.', '123 Provider Street', 'Provider City', 'Provider State', '12345', 'Provider Country', 'https://provider.example.com', NULL, 'active', 1, 0.00, 0, 0, 0, 0, NULL, '2025-05-25 13:05:35', '2025-05-25 13:05:35');

-- --------------------------------------------------------

--
-- Table structure for table `provider_locations`
--

CREATE TABLE `provider_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `emirate` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_locations`
--

INSERT INTO `provider_locations` (`id`, `provider_id`, `label`, `emirate`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, 3, NULL, 'Sharjah', 25.34753463, 55.40953831, '2025-05-24 13:09:10', '2025-05-24 13:09:10'),
(3, 3, NULL, 'Abu Dhabi', 25.34753463, 55.40953831, '2025-05-24 13:09:38', '2025-05-24 13:09:38');

-- --------------------------------------------------------

--
-- Table structure for table `provider_products`
--

CREATE TABLE `provider_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `original_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_products`
--

INSERT INTO `provider_products` (`id`, `provider_id`, `product_id`, `branch_id`, `status`, `created_at`, `updated_at`, `is_active`, `category_id`, `sku`, `stock`, `original_price`, `price`, `description`, `product_name`, `image`) VALUES
(6, 3, NULL, NULL, 'active', '2025-05-24 13:11:42', '2025-05-24 13:11:42', 1, NULL, 'B-009344', 55, 45.00, 40.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Bujma', 'images/provider_products/1748103102_Pray Clothes.jpg'),
(7, 4, NULL, NULL, 'active', '2025-05-24 13:14:22', '2025-05-24 13:14:22', 1, NULL, 'AM-235793', 44, 44.00, 44.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Apple Mask', 'images/provider_products/1748103262_Unlock the Power of Seamless Integration with Our….jfif'),
(8, 4, NULL, NULL, 'active', '2025-05-24 13:14:50', '2025-05-24 13:14:50', 1, NULL, 'M3-271035', 7, 89.00, 77.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Mask 3', 'images/provider_products/1748103290_Healthcare & Femtech.jpg'),
(9, 4, NULL, NULL, 'active', '2025-05-24 13:14:50', '2025-05-24 13:14:50', 1, NULL, 'M3-271035', 7, 89.00, 77.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Mask 3', 'images/provider_products/1748103290_Healthcare & Femtech.jpg'),
(10, 4, NULL, NULL, 'active', '2025-05-24 13:15:44', '2025-05-24 13:15:44', 1, NULL, 'JF-320228', 1, 80.00, 55.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Jelly Face', 'images/provider_products/1748103344_Beauty & Wellness Services.jpg'),
(11, 4, NULL, NULL, 'active', '2025-05-24 13:16:28', '2025-05-24 13:16:28', 1, NULL, 'SC-363012', 88, 70.00, 30.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Soft Cream', 'images/provider_products/1748103388_Spa Treatments.jpg'),
(12, 4, NULL, NULL, 'active', '2025-05-24 13:17:12', '2025-05-24 13:17:12', 1, NULL, 'GH-396377', 44, 55.00, 45.00, 'Food is a substance consumed to provide nutrients and energy for an organism. It can be raw, processed, or formulated and is ingested orally by animals to support growth, health, or pleasure. The body uses food to sustain growth, repair, and vital processes, and to provide energ', 'Goo Hijab', 'images/provider_products/1748103432_1733941151675.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `provider_profiles`
--

CREATE TABLE `provider_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `company_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_profiles`
--

INSERT INTO `provider_profiles` (`id`, `user_id`, `provider_id`, `product_name`, `stock`, `price`, `original_price`, `image`, `category_id`, `sku`, `description`, `is_active`, `created_at`, `updated_at`, `business_name`, `status`, `company_name`, `logo`, `contact_email`, `contact_phone`, `address`, `city`, `state`, `zip_code`, `country`) VALUES
(3, 12, 3, 'Default Product', 0, 0.00, NULL, NULL, NULL, NULL, NULL, 1, '2025-05-24 13:08:52', '2025-05-24 13:08:52', 'ِAmro Osman\'s Business', 'active', 'ِAmro Osman\'s Business', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 13, 4, 'Default Product', 0, 0.00, NULL, NULL, NULL, NULL, NULL, 1, '2025-05-24 13:14:22', '2025-05-24 13:14:22', 'Fwaz\'s Business', 'active', 'Fwaz\'s Business', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `provider_ratings`
--

CREATE TABLE `provider_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5',
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_ratings`
--

INSERT INTO `provider_ratings` (`id`, `vendor_id`, `provider_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 5, 'Test rating from API endpoint test', '2025-05-25 13:06:57', '2025-05-25 13:07:24'),
(2, 4, 4, 3, 'جميل جدا', '2025-05-25 13:12:50', '2025-05-25 13:13:16'),
(3, 4, 3, 1, 'good', '2025-05-25 13:13:47', '2025-05-25 17:43:18');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reviewable_type` varchar(255) NOT NULL,
  `reviewable_id` bigint(20) UNSIGNED NOT NULL,
  `rating` decimal(3,2) NOT NULL,
  `comment` text NOT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `likes` int(11) NOT NULL DEFAULT 0,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `home_service` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `branch_id`, `category_id`, `name`, `price`, `duration`, `featured`, `description`, `image`, `rating`, `is_available`, `home_service`, `created_at`, `updated_at`) VALUES
(1, 4, 107, 'Beginner Jewelry Making Workshop', 85.00, 180, 1, 'Discover the art of jewelry making in this comprehensive beginner-friendly workshop. Learn essential techniques including wire wrapping, beading, and basic metalworking. You\'ll create your own unique pieces including earrings, bracelets, and pendants using quality materials like sterling silver wire, gemstone beads, and crystals. Our experienced instructors will guide you through each step, from design concepts to finishing techniques. Perfect for those looking to explore a new creative hobby or develop skills for a potential business venture. All materials and tools are provided, and you\'ll leave with 3-4 completed pieces and the knowledge to continue crafting at home.', 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(2, 1, 107, 'Advanced Woodworking Masterclass', 150.00, 360, 0, 'Take your woodworking skills to the next level with this intensive masterclass designed for intermediate to advanced crafters. Learn sophisticated joinery techniques, advanced tool usage, and precision finishing methods. You\'ll work on a challenging project that incorporates dovetail joints, mortise and tenon connections, and hand-carved details. Our master craftsman will share professional tips for wood selection, grain matching, and achieving museum-quality finishes. This workshop covers both traditional hand tools and modern power tool techniques, ensuring you develop a well-rounded skill set. Perfect for furniture makers, cabinet builders, or anyone passionate about fine woodworking.', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(3, 6, 107, 'Textile Arts and Fiber Crafts', 95.00, 240, 0, 'Explore the rich world of textile arts in this comprehensive workshop covering multiple fiber craft techniques. Learn traditional methods including hand spinning, natural dyeing, basic weaving, and embroidery. You\'ll work with various fibers including wool, cotton, silk, and alpaca, understanding their unique properties and applications. The workshop includes instruction on using a spinning wheel, creating natural dyes from plants and minerals, and setting up a simple loom. Perfect for those interested in sustainable crafting, historical techniques, or developing a deeper connection with textile creation. You\'ll complete several small projects and gain the foundation to pursue any of these crafts further.', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(5, 1, 107, 'Leatherworking Fundamentals', 110.00, 300, 0, 'Learn the timeless craft of leatherworking in this comprehensive introduction to working with leather. Master essential techniques including cutting, stitching, tooling, and finishing leather goods. You\'ll work with high-quality vegetable-tanned leather to create functional items like wallets, belts, or small bags. The workshop covers tool selection and maintenance, leather types and grades, pattern making, and traditional hand-stitching methods. Learn decorative techniques such as stamping, carving, and edge finishing to create professional-looking pieces. Our experienced leather artisan will guide you through each step, ensuring you develop proper technique and safety practices. Perfect for those interested in traditional crafts, sustainable fashion, or creating personalized leather goods.', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(6, 6, 108, 'Watercolor Landscape Painting', 65.00, 120, 1, 'Immerse yourself in the beautiful world of watercolor landscape painting with this comprehensive class designed for all skill levels. Learn fundamental watercolor techniques including wet-on-wet, wet-on-dry, glazing, and color mixing to create stunning natural scenes. You\'ll explore composition principles, perspective, and how to capture light and atmosphere in your paintings. The class covers painting skies, trees, water reflections, and mountains using professional-grade watercolor paints and papers. Our experienced instructor will demonstrate various brush techniques and help you develop your own artistic style. Perfect for beginners wanting to learn watercolor basics or intermediate artists looking to refine their landscape skills. All materials provided including brushes, paints, and watercolor paper.', 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(7, 4, 108, 'Acrylic Portrait Painting Workshop', 95.00, 180, 0, 'Master the art of portrait painting using versatile acrylic paints in this intensive workshop. Learn essential techniques for capturing facial features, skin tones, and expressions with accuracy and artistic flair. The class covers facial anatomy, proportion guidelines, color theory for skin tones, and blending techniques specific to acrylic paints. You\'ll work from photo references to create a complete portrait, learning how to build layers, create depth, and achieve realistic textures. Our professional portrait artist will provide individual guidance on brush techniques, color mixing, and problem-solving. Suitable for intermediate to advanced painters who want to develop their portrait skills. This workshop provides a strong foundation for anyone interested in commissioned portrait work or fine art.', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(8, 1, 108, 'Abstract Expressionism Exploration', 80.00, 150, 0, 'Unleash your creativity and explore the freedom of abstract expressionism in this liberating painting class. Learn to express emotions, ideas, and energy through color, form, and gesture without the constraints of realistic representation. You\'ll experiment with various techniques including palette knife work, dripping, splattering, and gestural brushwork using acrylic and mixed media. The class covers color theory, composition in abstract work, and how to develop your personal artistic voice. Our instructor will guide you through exercises designed to break through creative blocks and develop confidence in non-representational art. Perfect for artists of all levels who want to explore contemporary art forms, develop intuitive painting skills, or simply enjoy the therapeutic benefits of expressive art-making.', 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(9, 6, 108, 'Oil Painting Still Life Masterclass', 120.00, 240, 0, 'Develop classical painting skills with this comprehensive oil painting still life class. Learn traditional techniques used by master painters including color mixing, glazing, scumbling, and alla prima methods. You\'ll work with professional oil paints to create a detailed still life composition, focusing on light, shadow, texture, and form. The class covers canvas preparation, color temperature, brushwork techniques, and the unique properties of oil paint including blending times and layering methods. Our classically trained instructor will demonstrate time-tested approaches while helping you develop your own artistic interpretation. Perfect for serious art students, professional artists wanting to refine their skills, or anyone passionate about traditional painting methods. This intensive class provides excellent preparation for advanced art studies.', 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(10, 4, 108, 'Plein Air Painting Adventure', 85.00, 180, 0, 'Experience the joy and challenge of painting outdoors with this plein air painting class that combines artistic instruction with nature exploration. Learn to quickly capture changing light, weather conditions, and natural scenes using portable painting techniques. You\'ll master rapid color mixing, simplified composition methods, and how to work efficiently in various outdoor conditions. The class covers essential plein air equipment, color temperature changes throughout the day, and techniques for finishing paintings in the studio. Our experienced plein air artist will guide you through location selection, setup procedures, and adapting to environmental challenges. Perfect for landscape painters, nature lovers, or anyone wanting to develop observational skills and connect with the environment through art. Weather-appropriate locations selected, and backup indoor options available.', 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(12, 6, 109, 'Wedding Photography Masterclass', 200.00, 300, 0, 'Master the art of wedding photography with this comprehensive workshop covering all aspects of capturing one of life\'s most important celebrations. Learn essential techniques for photographing ceremonies, receptions, and intimate moments while working in challenging lighting conditions. The class covers timeline planning, shot lists, working with couples, and managing family group photos efficiently. You\'ll practice with professional wedding photography equipment and learn backup strategies for critical moments. Our experienced wedding photographer will share business insights, client communication skills, and post-processing workflows specific to wedding photography. Perfect for photographers looking to enter the wedding industry or improve their event photography skills. Includes hands-on practice with mock wedding scenarios.', 'https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(13, 1, 109, 'Nature and Wildlife Photography', 120.00, 240, 0, 'Explore the fascinating world of nature and wildlife photography in this outdoor workshop designed for photographers of all levels. Learn specialized techniques for capturing animals in their natural habitat, including telephoto lens usage, camouflage strategies, and ethical wildlife photography practices. The workshop covers macro photography for insects and flowers, landscape composition, and working with natural light throughout the day. You\'ll practice patience, observation skills, and quick reflexes needed for successful wildlife photography. Our expert nature photographer will share field techniques, safety considerations, and conservation awareness. Perfect for nature lovers, outdoor enthusiasts, or photographers wanting to specialize in environmental subjects. Weather-dependent with backup indoor macro photography sessions available.', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(14, 6, 109, 'Street Photography Workshop', 95.00, 180, 0, 'Discover the art of street photography and learn to capture authentic moments of urban life with this dynamic workshop. Master techniques for photographing people, architecture, and street scenes while developing your eye for decisive moments and compelling compositions. The workshop covers camera settings for fast-moving situations, working with available light, and approaching subjects respectfully. You\'ll learn about the legal and ethical aspects of street photography, building confidence to photograph in public spaces. Our experienced street photographer will guide you through various urban environments, teaching you to anticipate action and capture the essence of city life. Perfect for documentary photographers, travel enthusiasts, or anyone interested in photojournalism and social photography.', 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(15, 1, 109, 'Product Photography for E-commerce', 110.00, 150, 0, 'Learn professional product photography techniques essential for e-commerce success in this practical workshop. Master lighting setups, background selection, and styling techniques that make products look their best online. You\'ll work with various product types including jewelry, clothing, electronics, and food items, learning specific approaches for each category. The workshop covers camera settings, white balance, and creating consistent lighting for product catalogs. Our commercial photographer will teach you cost-effective lighting solutions, DIY studio setups, and post-processing workflows for high-volume product photography. Perfect for small business owners, online sellers, or photographers wanting to enter commercial photography. Includes guidance on building a profitable product photography business.', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(16, 1, 110, 'Wheel Throwing for Beginners', 85.00, 180, 1, 'Discover the meditative art of pottery wheel throwing in this hands-on beginner class. Learn fundamental techniques for centering clay, opening forms, and pulling walls to create functional pottery pieces like bowls, cups, and vases. You\'ll master the basics of clay preparation, wheel speed control, and proper body positioning for successful throwing. The class covers trimming techniques, handle attachment, and basic glazing principles. Our experienced potter will guide you through each step, helping you develop muscle memory and confidence on the wheel. Perfect for complete beginners or those wanting to refresh their pottery skills. The therapeutic nature of working with clay provides stress relief while developing a valuable artistic skill. All clay, tools, and firing included, with finished pieces ready for pickup after glazing and firing.', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(17, 4, 110, 'Advanced Ceramic Sculpture', 150.00, 300, 0, 'Push the boundaries of ceramic art with this advanced sculpture class focusing on large-scale and complex ceramic forms. Learn sophisticated hand-building techniques including coil construction, slab building, and hollow form creation. You\'ll explore surface treatments, texture techniques, and experimental glazing methods to create unique artistic pieces. The class covers armature construction, drying techniques for large pieces, and kiln loading strategies for sculptural work. Our master ceramicist will guide you through conceptual development, helping you translate ideas into clay while solving technical challenges. Perfect for experienced potters ready to explore sculptural possibilities or artists from other mediums wanting to work with clay. Includes instruction on alternative firing techniques and contemporary ceramic art practices.', '/images/services/1748953344_Pottery making 1.jpg', 4.90, 1, 0, '2025-06-03 08:45:05', '2025-06-03 09:22:24'),
(18, 3, 110, 'Glazing and Surface Design Workshop', 95.00, 240, 0, 'Explore the magical world of ceramic glazes and surface treatments in this comprehensive workshop. Learn glaze chemistry basics, application techniques, and how different glazes interact with clay bodies and firing temperatures. You\'ll experiment with various glazing methods including dipping, brushing, pouring, and resist techniques to create unique surface effects. The workshop covers glaze layering, crystalline glazes, and troubleshooting common glazing problems. Our glaze specialist will demonstrate advanced techniques like raku firing, saggar firing, and alternative atmospheric effects. Perfect for potters wanting to expand their surface treatment knowledge or ceramic artists looking to develop signature glaze effects. Includes extensive testing and documentation to help you recreate successful results in your own work.', 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(19, 1, 110, 'Functional Pottery Workshop', 100.00, 210, 0, 'Create beautiful, functional pottery pieces for everyday use in this practical workshop focused on utilitarian ceramics. Learn to make dinnerware sets, serving pieces, and kitchen accessories that combine beauty with functionality. You\'ll master techniques for creating consistent forms, proper proportions for functional use, and ergonomic considerations for handles and spouts. The workshop covers food-safe glazing, durability testing, and design principles that make pottery both attractive and practical. Our functional potter will share insights about developing a cohesive style, pricing handmade pottery, and building a customer base for functional ceramics. Perfect for potters interested in creating sellable work or anyone wanting to make personalized pottery for their home. Includes business guidance for those interested in selling their pottery.', 'https://images.unsplash.com/photo-1493106819501-66d381c466f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(20, 6, 110, 'Kids Clay Fun Class', 45.00, 90, 0, 'Introduce children to the joy of working with clay in this fun, educational pottery class designed specifically for young artists ages 6-12. Kids will learn basic hand-building techniques through playful projects like pinch pots, coil animals, and slab tiles. The class emphasizes creativity, self-expression, and the satisfaction of creating something with their hands. Children will explore texture tools, stamps, and simple glazing techniques to decorate their pieces. Our patient, experienced instructor creates a supportive environment where kids can experiment freely while learning fundamental clay skills. Perfect for developing fine motor skills, creativity, and confidence in artistic expression. All materials provided, and parents receive tips for continuing clay exploration at home. Finished pieces are fired and ready for pickup within two weeks.', 'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(21, 3, 112, 'Weekly Companionship Sessions', 45.00, 120, 1, 'Provide meaningful social interaction and emotional support through regular companionship visits designed to combat loneliness and enhance quality of life for elderly individuals. Our trained companions offer engaging conversation, light activities, and genuine friendship to seniors who may be isolated or have limited social contact. Sessions include playing board games, sharing stories, looking through photo albums, or simply enjoying pleasant conversation over tea. We focus on building trust and rapport while respecting individual preferences and interests. Our companions are background-checked, trained in elderly care basics, and skilled in active listening and empathy. Perfect for families seeking reliable social support for their loved ones or seniors wanting to maintain social connections. Each visit is tailored to the individual\'s interests, mobility level, and emotional needs, ensuring a positive and enriching experience.', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(22, 3, 112, 'Memory Care Companionship', 55.00, 90, 0, 'Specialized companionship services for individuals with dementia, Alzheimer\'s, or other memory-related conditions, focusing on maintaining dignity, reducing anxiety, and providing cognitive stimulation. Our memory care companions are trained in dementia care techniques, validation therapy, and creating calm, supportive environments. Activities are designed to engage remaining cognitive abilities while providing comfort and familiarity. Sessions may include reminiscence therapy, simple crafts, music therapy, or gentle physical activities adapted to the individual\'s current abilities. We work closely with families and healthcare providers to ensure consistency and appropriate care approaches. Our companions understand the unique challenges of memory loss and are skilled in redirecting confusion, managing behavioral changes, and maintaining patient, compassionate interactions throughout the progression of the condition.', '/images/services/1748953390_Memory Care Companionship.jpg', 4.80, 1, 1, '2025-06-03 08:45:05', '2025-06-03 09:23:10'),
(23, 4, 112, 'Social Outing Assistance', 65.00, 180, 0, 'Accompany elderly individuals on social outings and community activities to help them maintain independence and social connections while ensuring safety and support. Our companions provide transportation assistance, mobility support, and social facilitation during visits to shopping centers, restaurants, cultural events, or medical appointments. We help navigate physical challenges, provide emotional support in social situations, and ensure our clients feel confident and comfortable during outings. Services include assistance with walking, carrying items, communication support, and emergency response if needed. Our companions are trained in elderly mobility assistance, emergency procedures, and social interaction facilitation. Perfect for seniors who want to remain active in their community but need additional support or confidence to participate in social activities safely and enjoyably.', 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(24, 1, 112, 'Technology Assistance and Digital Companionship', 50.00, 90, 0, 'Help elderly individuals stay connected with family and friends through technology while providing patient, personalized instruction in using digital devices and platforms. Our tech-savvy companions teach smartphone usage, video calling, social media basics, and online safety in a supportive, non-judgmental environment. Sessions include hands-on practice with devices, setting up accounts, organizing digital photos, and troubleshooting common issues. We focus on building confidence and independence in technology use while maintaining the human connection that makes learning enjoyable. Our companions understand the unique challenges seniors face with technology and provide step-by-step guidance at a comfortable pace. Perfect for seniors wanting to stay connected with distant family members, explore online interests, or simply feel more confident using modern technology in their daily lives.', 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(25, 3, 112, 'Grief and Loss Support Companionship', 60.00, 120, 0, 'Provide compassionate support and companionship for elderly individuals dealing with grief, loss, or major life transitions such as the death of a spouse, moving to assisted living, or declining health. Our specially trained companions offer emotional support, active listening, and gentle encouragement during difficult times. We understand that grief affects everyone differently and provide non-judgmental presence while respecting individual coping styles and timelines. Sessions may include sharing memories, light activities to provide distraction, assistance with practical tasks, or simply sitting quietly together. Our companions are trained in grief support techniques, crisis intervention, and recognizing when additional professional help may be needed. Perfect for families seeking additional emotional support for their loved ones during challenging transitions or for seniors who need someone to talk to during difficult times.', 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(26, 5, 113, 'Personal Care Assistance', 75.00, 240, 1, 'Comprehensive personal care services designed to help elderly individuals maintain dignity and independence while receiving assistance with daily living activities in the comfort of their own homes. Our certified caregivers provide professional support with bathing, dressing, grooming, toileting, and mobility assistance while respecting privacy and individual preferences. We focus on maintaining the highest standards of hygiene, safety, and comfort while encouraging independence wherever possible. Our caregivers are trained in proper body mechanics, infection control, and emergency response procedures. Each care plan is customized to the individual\'s specific needs, health conditions, and family preferences. We work closely with healthcare providers to ensure continuity of care and monitor for changes in condition. Perfect for seniors who want to age in place while receiving the support they need to maintain their quality of life and personal dignity.', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(27, 1, 113, 'Medication Management and Health Monitoring', 85.00, 180, 0, 'Professional medication management and health monitoring services to ensure elderly individuals take medications correctly and maintain optimal health while living independently. Our trained caregivers assist with medication organization, reminder systems, and monitoring for side effects or changes in condition. We maintain detailed records of medication administration, vital signs, and health observations to share with healthcare providers and family members. Services include blood pressure monitoring, blood sugar testing for diabetics, weight tracking, and general health assessments. Our caregivers are trained to recognize signs of medical emergencies and respond appropriately. We coordinate with pharmacies, doctors, and family members to ensure comprehensive care coordination. Perfect for seniors with complex medication regimens, chronic conditions, or those recovering from illness or surgery who need professional health monitoring in their home environment.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(28, 3, 113, 'Household Management and Light Housekeeping', 65.00, 180, 0, 'Comprehensive household management services to help elderly individuals maintain a clean, safe, and organized living environment while preserving their independence and comfort at home. Our caregivers provide light housekeeping including dusting, vacuuming, bathroom cleaning, kitchen maintenance, and laundry services. We also assist with meal planning, grocery shopping, and basic meal preparation tailored to dietary restrictions and preferences. Services include organizing medications, managing appointments, and maintaining household supplies. Our team focuses on creating a safe environment by identifying and addressing potential hazards while respecting the individual\'s personal space and belongings. We work with family members to establish routines and preferences that support the senior\'s lifestyle and independence. Perfect for elderly individuals who need assistance maintaining their home environment but want to continue living independently.', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(30, 1, 113, 'Respite Care for Family Caregivers', 80.00, 300, 0, 'Professional respite care services providing temporary relief for family caregivers while ensuring their elderly loved ones receive quality care and supervision in their own homes. Our experienced caregivers step in to provide all necessary care services, allowing family members to take breaks, attend to personal needs, or simply rest and recharge. We maintain the established care routines and preferences while providing professional oversight and assistance. Services can range from a few hours to overnight care, depending on family needs. Our caregivers are trained to handle various care levels from basic companionship to complex medical needs, ensuring seamless care transitions. We provide detailed reports to family members about the care provided and any observations about their loved one\'s condition. Perfect for family caregivers who need regular breaks to maintain their own health and well-being while ensuring their loved one receives consistent, professional care.', 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 1, '2025-06-03 08:45:05', '2025-06-03 08:45:05'),
(31, 6, 107, 'Beginner Jewelry Making Workshop', 85.00, 180, 1, 'Discover the art of jewelry making in this comprehensive beginner-friendly workshop. Learn essential techniques including wire wrapping, beading, and basic metalworking. You\'ll create your own unique pieces including earrings, bracelets, and pendants using quality materials like sterling silver wire, gemstone beads, and crystals. Our experienced instructors will guide you through each step, from design concepts to finishing techniques. Perfect for those looking to explore a new creative hobby or develop skills for a potential business venture. All materials and tools are provided, and you\'ll leave with 3-4 completed pieces and the knowledge to continue crafting at home.', 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(32, 5, 107, 'Advanced Woodworking Masterclass', 150.00, 360, 0, 'Take your woodworking skills to the next level with this intensive masterclass designed for intermediate to advanced crafters. Learn sophisticated joinery techniques, advanced tool usage, and precision finishing methods. You\'ll work on a challenging project that incorporates dovetail joints, mortise and tenon connections, and hand-carved details. Our master craftsman will share professional tips for wood selection, grain matching, and achieving museum-quality finishes. This workshop covers both traditional hand tools and modern power tool techniques, ensuring you develop a well-rounded skill set. Perfect for furniture makers, cabinet builders, or anyone passionate about fine woodworking.', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(33, 6, 107, 'Textile Arts and Fiber Crafts', 95.00, 240, 0, 'Explore the rich world of textile arts in this comprehensive workshop covering multiple fiber craft techniques. Learn traditional methods including hand spinning, natural dyeing, basic weaving, and embroidery. You\'ll work with various fibers including wool, cotton, silk, and alpaca, understanding their unique properties and applications. The workshop includes instruction on using a spinning wheel, creating natural dyes from plants and minerals, and setting up a simple loom. Perfect for those interested in sustainable crafting, historical techniques, or developing a deeper connection with textile creation. You\'ll complete several small projects and gain the foundation to pursue any of these crafts further.', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(35, 4, 107, 'Leatherworking Fundamentals', 110.00, 300, 0, 'Learn the timeless craft of leatherworking in this comprehensive introduction to working with leather. Master essential techniques including cutting, stitching, tooling, and finishing leather goods. You\'ll work with high-quality vegetable-tanned leather to create functional items like wallets, belts, or small bags. The workshop covers tool selection and maintenance, leather types and grades, pattern making, and traditional hand-stitching methods. Learn decorative techniques such as stamping, carving, and edge finishing to create professional-looking pieces. Our experienced leather artisan will guide you through each step, ensuring you develop proper technique and safety practices. Perfect for those interested in traditional crafts, sustainable fashion, or creating personalized leather goods.', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(36, 1, 108, 'Watercolor Landscape Painting', 65.00, 120, 1, 'Immerse yourself in the beautiful world of watercolor landscape painting with this comprehensive class designed for all skill levels. Learn fundamental watercolor techniques including wet-on-wet, wet-on-dry, glazing, and color mixing to create stunning natural scenes. You\'ll explore composition principles, perspective, and how to capture light and atmosphere in your paintings. The class covers painting skies, trees, water reflections, and mountains using professional-grade watercolor paints and papers. Our experienced instructor will demonstrate various brush techniques and help you develop your own artistic style. Perfect for beginners wanting to learn watercolor basics or intermediate artists looking to refine their landscape skills. All materials provided including brushes, paints, and watercolor paper.', 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(37, 2, 108, 'Acrylic Portrait Painting Workshop', 95.00, 180, 0, 'Master the art of portrait painting using versatile acrylic paints in this intensive workshop. Learn essential techniques for capturing facial features, skin tones, and expressions with accuracy and artistic flair. The class covers facial anatomy, proportion guidelines, color theory for skin tones, and blending techniques specific to acrylic paints. You\'ll work from photo references to create a complete portrait, learning how to build layers, create depth, and achieve realistic textures. Our professional portrait artist will provide individual guidance on brush techniques, color mixing, and problem-solving. Suitable for intermediate to advanced painters who want to develop their portrait skills. This workshop provides a strong foundation for anyone interested in commissioned portrait work or fine art.', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(38, 4, 108, 'Abstract Expressionism Exploration', 80.00, 150, 0, 'Unleash your creativity and explore the freedom of abstract expressionism in this liberating painting class. Learn to express emotions, ideas, and energy through color, form, and gesture without the constraints of realistic representation. You\'ll experiment with various techniques including palette knife work, dripping, splattering, and gestural brushwork using acrylic and mixed media. The class covers color theory, composition in abstract work, and how to develop your personal artistic voice. Our instructor will guide you through exercises designed to break through creative blocks and develop confidence in non-representational art. Perfect for artists of all levels who want to explore contemporary art forms, develop intuitive painting skills, or simply enjoy the therapeutic benefits of expressive art-making.', 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(39, 2, 108, 'Oil Painting Still Life Masterclass', 120.00, 240, 0, 'Develop classical painting skills with this comprehensive oil painting still life class. Learn traditional techniques used by master painters including color mixing, glazing, scumbling, and alla prima methods. You\'ll work with professional oil paints to create a detailed still life composition, focusing on light, shadow, texture, and form. The class covers canvas preparation, color temperature, brushwork techniques, and the unique properties of oil paint including blending times and layering methods. Our classically trained instructor will demonstrate time-tested approaches while helping you develop your own artistic interpretation. Perfect for serious art students, professional artists wanting to refine their skills, or anyone passionate about traditional painting methods. This intensive class provides excellent preparation for advanced art studies.', 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(40, 5, 108, 'Plein Air Painting Adventure', 85.00, 180, 0, 'Experience the joy and challenge of painting outdoors with this plein air painting class that combines artistic instruction with nature exploration. Learn to quickly capture changing light, weather conditions, and natural scenes using portable painting techniques. You\'ll master rapid color mixing, simplified composition methods, and how to work efficiently in various outdoor conditions. The class covers essential plein air equipment, color temperature changes throughout the day, and techniques for finishing paintings in the studio. Our experienced plein air artist will guide you through location selection, setup procedures, and adapting to environmental challenges. Perfect for landscape painters, nature lovers, or anyone wanting to develop observational skills and connect with the environment through art. Weather-appropriate locations selected, and backup indoor options available.', 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(42, 1, 109, 'Wedding Photography Masterclass', 200.00, 300, 0, 'Master the art of wedding photography with this comprehensive workshop covering all aspects of capturing one of life\'s most important celebrations. Learn essential techniques for photographing ceremonies, receptions, and intimate moments while working in challenging lighting conditions. The class covers timeline planning, shot lists, working with couples, and managing family group photos efficiently. You\'ll practice with professional wedding photography equipment and learn backup strategies for critical moments. Our experienced wedding photographer will share business insights, client communication skills, and post-processing workflows specific to wedding photography. Perfect for photographers looking to enter the wedding industry or improve their event photography skills. Includes hands-on practice with mock wedding scenarios.', 'https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(43, 2, 109, 'Nature and Wildlife Photography', 120.00, 240, 0, 'Explore the fascinating world of nature and wildlife photography in this outdoor workshop designed for photographers of all levels. Learn specialized techniques for capturing animals in their natural habitat, including telephoto lens usage, camouflage strategies, and ethical wildlife photography practices. The workshop covers macro photography for insects and flowers, landscape composition, and working with natural light throughout the day. You\'ll practice patience, observation skills, and quick reflexes needed for successful wildlife photography. Our expert nature photographer will share field techniques, safety considerations, and conservation awareness. Perfect for nature lovers, outdoor enthusiasts, or photographers wanting to specialize in environmental subjects. Weather-dependent with backup indoor macro photography sessions available.', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(44, 2, 109, 'Street Photography Workshop', 95.00, 180, 0, 'Discover the art of street photography and learn to capture authentic moments of urban life with this dynamic workshop. Master techniques for photographing people, architecture, and street scenes while developing your eye for decisive moments and compelling compositions. The workshop covers camera settings for fast-moving situations, working with available light, and approaching subjects respectfully. You\'ll learn about the legal and ethical aspects of street photography, building confidence to photograph in public spaces. Our experienced street photographer will guide you through various urban environments, teaching you to anticipate action and capture the essence of city life. Perfect for documentary photographers, travel enthusiasts, or anyone interested in photojournalism and social photography.', 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(45, 4, 109, 'Product Photography for E-commerce', 110.00, 150, 0, 'Learn professional product photography techniques essential for e-commerce success in this practical workshop. Master lighting setups, background selection, and styling techniques that make products look their best online. You\'ll work with various product types including jewelry, clothing, electronics, and food items, learning specific approaches for each category. The workshop covers camera settings, white balance, and creating consistent lighting for product catalogs. Our commercial photographer will teach you cost-effective lighting solutions, DIY studio setups, and post-processing workflows for high-volume product photography. Perfect for small business owners, online sellers, or photographers wanting to enter commercial photography. Includes guidance on building a profitable product photography business.', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(46, 4, 110, 'Wheel Throwing for Beginners', 85.00, 180, 1, 'Discover the meditative art of pottery wheel throwing in this hands-on beginner class. Learn fundamental techniques for centering clay, opening forms, and pulling walls to create functional pottery pieces like bowls, cups, and vases. You\'ll master the basics of clay preparation, wheel speed control, and proper body positioning for successful throwing. The class covers trimming techniques, handle attachment, and basic glazing principles. Our experienced potter will guide you through each step, helping you develop muscle memory and confidence on the wheel. Perfect for complete beginners or those wanting to refresh their pottery skills. The therapeutic nature of working with clay provides stress relief while developing a valuable artistic skill. All clay, tools, and firing included, with finished pieces ready for pickup after glazing and firing.', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(48, 3, 110, 'Glazing and Surface Design Workshop', 95.00, 240, 0, 'Explore the magical world of ceramic glazes and surface treatments in this comprehensive workshop. Learn glaze chemistry basics, application techniques, and how different glazes interact with clay bodies and firing temperatures. You\'ll experiment with various glazing methods including dipping, brushing, pouring, and resist techniques to create unique surface effects. The workshop covers glaze layering, crystalline glazes, and troubleshooting common glazing problems. Our glaze specialist will demonstrate advanced techniques like raku firing, saggar firing, and alternative atmospheric effects. Perfect for potters wanting to expand their surface treatment knowledge or ceramic artists looking to develop signature glaze effects. Includes extensive testing and documentation to help you recreate successful results in your own work.', 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(49, 5, 110, 'Functional Pottery Workshop', 100.00, 210, 0, 'Create beautiful, functional pottery pieces for everyday use in this practical workshop focused on utilitarian ceramics. Learn to make dinnerware sets, serving pieces, and kitchen accessories that combine beauty with functionality. You\'ll master techniques for creating consistent forms, proper proportions for functional use, and ergonomic considerations for handles and spouts. The workshop covers food-safe glazing, durability testing, and design principles that make pottery both attractive and practical. Our functional potter will share insights about developing a cohesive style, pricing handmade pottery, and building a customer base for functional ceramics. Perfect for potters interested in creating sellable work or anyone wanting to make personalized pottery for their home. Includes business guidance for those interested in selling their pottery.', 'https://images.unsplash.com/photo-1493106819501-66d381c466f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(50, 3, 110, 'Kids Clay Fun Class', 45.00, 90, 0, 'Introduce children to the joy of working with clay in this fun, educational pottery class designed specifically for young artists ages 6-12. Kids will learn basic hand-building techniques through playful projects like pinch pots, coil animals, and slab tiles. The class emphasizes creativity, self-expression, and the satisfaction of creating something with their hands. Children will explore texture tools, stamps, and simple glazing techniques to decorate their pieces. Our patient, experienced instructor creates a supportive environment where kids can experiment freely while learning fundamental clay skills. Perfect for developing fine motor skills, creativity, and confidence in artistic expression. All materials provided, and parents receive tips for continuing clay exploration at home. Finished pieces are fired and ready for pickup within two weeks.', 'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(51, 3, 112, 'Weekly Companionship Sessions', 45.00, 120, 1, 'Provide meaningful social interaction and emotional support through regular companionship visits designed to combat loneliness and enhance quality of life for elderly individuals. Our trained companions offer engaging conversation, light activities, and genuine friendship to seniors who may be isolated or have limited social contact. Sessions include playing board games, sharing stories, looking through photo albums, or simply enjoying pleasant conversation over tea. We focus on building trust and rapport while respecting individual preferences and interests. Our companions are background-checked, trained in elderly care basics, and skilled in active listening and empathy. Perfect for families seeking reliable social support for their loved ones or seniors wanting to maintain social connections. Each visit is tailored to the individual\'s interests, mobility level, and emotional needs, ensuring a positive and enriching experience.', '/images/services/1748953720_Weekly Companionship Sessions.jpg', 4.90, 1, 1, '2025-06-03 08:46:27', '2025-06-03 09:28:40'),
(53, 6, 112, 'Social Outing Assistance', 65.00, 180, 0, 'Accompany elderly individuals on social outings and community activities to help them maintain independence and social connections while ensuring safety and support. Our companions provide transportation assistance, mobility support, and social facilitation during visits to shopping centers, restaurants, cultural events, or medical appointments. We help navigate physical challenges, provide emotional support in social situations, and ensure our clients feel confident and comfortable during outings. Services include assistance with walking, carrying items, communication support, and emergency response if needed. Our companions are trained in elderly mobility assistance, emergency procedures, and social interaction facilitation. Perfect for seniors who want to remain active in their community but need additional support or confidence to participate in social activities safely and enjoyably.', 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:27', '2025-06-03 08:46:27');
INSERT INTO `services` (`id`, `branch_id`, `category_id`, `name`, `price`, `duration`, `featured`, `description`, `image`, `rating`, `is_available`, `home_service`, `created_at`, `updated_at`) VALUES
(54, 5, 112, 'Technology Assistance and Digital Companionship', 50.00, 90, 0, 'Help elderly individuals stay connected with family and friends through technology while providing patient, personalized instruction in using digital devices and platforms. Our tech-savvy companions teach smartphone usage, video calling, social media basics, and online safety in a supportive, non-judgmental environment. Sessions include hands-on practice with devices, setting up accounts, organizing digital photos, and troubleshooting common issues. We focus on building confidence and independence in technology use while maintaining the human connection that makes learning enjoyable. Our companions understand the unique challenges seniors face with technology and provide step-by-step guidance at a comfortable pace. Perfect for seniors wanting to stay connected with distant family members, explore online interests, or simply feel more confident using modern technology in their daily lives.', 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 1, '2025-06-03 08:46:27', '2025-06-03 08:46:27'),
(55, 2, 112, 'Grief and Loss Support Companionship', 60.00, 120, 0, 'Provide compassionate support and companionship for elderly individuals dealing with grief, loss, or major life transitions such as the death of a spouse, moving to assisted living, or declining health. Our specially trained companions offer emotional support, active listening, and gentle encouragement during difficult times. We understand that grief affects everyone differently and provide non-judgmental presence while respecting individual coping styles and timelines. Sessions may include sharing memories, light activities to provide distraction, assistance with practical tasks, or simply sitting quietly together. Our companions are trained in grief support techniques, crisis intervention, and recognizing when additional professional help may be needed. Perfect for families seeking additional emotional support for their loved ones during challenging transitions or for seniors who need someone to talk to during difficult times.', 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 1, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(56, 5, 113, 'Personal Care Assistance', 75.00, 240, 1, 'Comprehensive personal care services designed to help elderly individuals maintain dignity and independence while receiving assistance with daily living activities in the comfort of their own homes. Our certified caregivers provide professional support with bathing, dressing, grooming, toileting, and mobility assistance while respecting privacy and individual preferences. We focus on maintaining the highest standards of hygiene, safety, and comfort while encouraging independence wherever possible. Our caregivers are trained in proper body mechanics, infection control, and emergency response procedures. Each care plan is customized to the individual\'s specific needs, health conditions, and family preferences. We work closely with healthcare providers to ensure continuity of care and monitor for changes in condition. Perfect for seniors who want to age in place while receiving the support they need to maintain their quality of life and personal dignity.', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 1, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(57, 5, 113, 'Medication Management and Health Monitoring', 85.00, 180, 0, 'Professional medication management and health monitoring services to ensure elderly individuals take medications correctly and maintain optimal health while living independently. Our trained caregivers assist with medication organization, reminder systems, and monitoring for side effects or changes in condition. We maintain detailed records of medication administration, vital signs, and health observations to share with healthcare providers and family members. Services include blood pressure monitoring, blood sugar testing for diabetics, weight tracking, and general health assessments. Our caregivers are trained to recognize signs of medical emergencies and respond appropriately. We coordinate with pharmacies, doctors, and family members to ensure comprehensive care coordination. Perfect for seniors with complex medication regimens, chronic conditions, or those recovering from illness or surgery who need professional health monitoring in their home environment.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 1, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(58, 2, 113, 'Household Management and Light Housekeeping', 65.00, 180, 0, 'Comprehensive household management services to help elderly individuals maintain a clean, safe, and organized living environment while preserving their independence and comfort at home. Our caregivers provide light housekeeping including dusting, vacuuming, bathroom cleaning, kitchen maintenance, and laundry services. We also assist with meal planning, grocery shopping, and basic meal preparation tailored to dietary restrictions and preferences. Services include organizing medications, managing appointments, and maintaining household supplies. Our team focuses on creating a safe environment by identifying and addressing potential hazards while respecting the individual\'s personal space and belongings. We work with family members to establish routines and preferences that support the senior\'s lifestyle and independence. Perfect for elderly individuals who need assistance maintaining their home environment but want to continue living independently.', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 1, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(60, 3, 113, 'Respite Care for Family Caregivers', 80.00, 300, 0, 'Professional respite care services providing temporary relief for family caregivers while ensuring their elderly loved ones receive quality care and supervision in their own homes. Our experienced caregivers step in to provide all necessary care services, allowing family members to take breaks, attend to personal needs, or simply rest and recharge. We maintain the established care routines and preferences while providing professional oversight and assistance. Services can range from a few hours to overnight care, depending on family needs. Our caregivers are trained to handle various care levels from basic companionship to complex medical needs, ensuring seamless care transitions. We provide detailed reports to family members about the care provided and any observations about their loved one\'s condition. Perfect for family caregivers who need regular breaks to maintain their own health and well-being while ensuring their loved one receives consistent, professional care.', 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 1, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(61, 6, 115, 'Beginner Mat Pilates', 25.00, 60, 1, 'Introduction to Pilates fundamentals focusing on core strength, flexibility, and body awareness using mat-based exercises. This beginner-friendly class teaches proper breathing techniques, basic Pilates principles, and foundational movements that form the basis of all Pilates practice. You\'ll learn to engage your powerhouse (core muscles), improve posture, and develop mind-body connection through controlled, precise movements. Our certified instructor provides modifications for all fitness levels and physical limitations, ensuring everyone can participate safely and effectively. The class emphasizes quality over quantity, teaching you to move with intention and control. Perfect for those new to Pilates, recovering from injury, or anyone wanting to build a strong foundation in this transformative exercise method. Regular practice improves flexibility, strength, balance, and overall body awareness.', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(62, 2, 115, 'Reformer Pilates Intermediate', 45.00, 55, 0, 'Challenge your Pilates practice with this intermediate reformer class that utilizes spring resistance and moving carriage to deepen your workout and enhance muscle engagement. The reformer provides both assistance and resistance, allowing for more precise muscle targeting and increased exercise variety. You\'ll work on advanced movement patterns, complex sequences, and challenging positions that build significant strength and flexibility. This class requires previous Pilates experience and good understanding of basic principles. Our expert instructor guides you through flowing sequences that challenge stability, coordination, and strength while maintaining the precision and control that defines Pilates. Perfect for those ready to advance their practice and experience the unique benefits of reformer training. The reformer\'s versatility allows for modifications and progressions to suit individual needs and goals.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(65, 6, 115, 'Pilates Rehabilitation and Injury Recovery', 40.00, 60, 0, 'Therapeutic Pilates class designed for individuals recovering from injury or dealing with chronic pain conditions, focusing on safe movement patterns and gradual strength building. This specialized class works closely with physical therapy principles to support healing and prevent re-injury. Our instructor, trained in rehabilitation techniques, assesses individual needs and provides personalized modifications and progressions. The class emphasizes proper alignment, gentle strengthening, and mobility restoration while respecting healing timelines and medical restrictions. Common conditions addressed include back pain, neck issues, joint problems, and post-surgical recovery. Perfect for those cleared by their healthcare provider to begin gentle exercise or anyone dealing with chronic pain who needs a careful, knowledgeable approach to movement. The therapeutic benefits of Pilates can significantly support recovery and long-term pain management when practiced correctly.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(66, 4, 116, 'Beginner Strength Training Fundamentals', 35.00, 75, 1, 'Comprehensive introduction to strength training covering proper form, safety protocols, and fundamental movement patterns essential for building a strong foundation in resistance exercise. This beginner-friendly class teaches the basics of weightlifting including squat, deadlift, bench press, and overhead press techniques using barbells, dumbbells, and bodyweight exercises. You\'ll learn about progressive overload, workout structure, and how to design effective training programs. Our certified trainer provides individual attention to ensure proper form and prevent injury while building confidence in the weight room. The class covers equipment familiarization, warm-up and cool-down protocols, and basic nutrition principles for muscle building. Perfect for complete beginners, those returning to exercise after a break, or anyone wanting to learn proper lifting techniques. Regular participation builds functional strength, improves bone density, and boosts metabolism for long-term health benefits.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(67, 1, 116, 'Advanced Powerlifting Training', 55.00, 90, 0, 'Intensive powerlifting-focused training for experienced lifters looking to maximize strength in the squat, bench press, and deadlift through advanced programming and technique refinement. This class is designed for serious strength athletes who want to compete or simply achieve their maximum strength potential. You\'ll work with percentage-based programming, learn advanced techniques like pause reps and tempo work, and receive coaching on competition-style lifting. Our powerlifting coach provides individualized feedback on form, helps identify and correct weaknesses, and guides you through periodized training cycles. The class covers meet preparation, equipment usage, and mental preparation for maximum lifts. Perfect for experienced lifters ready to take their strength to the next level or those interested in powerlifting competition. Requires solid foundation in basic lifts and ability to handle heavy weights safely.', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(68, 1, 116, 'Functional Strength for Daily Life', 30.00, 60, 0, 'Practical strength training focused on movements and exercises that directly improve your ability to perform daily activities with ease and confidence. This functional approach emphasizes multi-joint movements, core stability, and real-world strength applications rather than isolated muscle building. You\'ll practice movements like lifting, carrying, pushing, pulling, and climbing using various equipment including kettlebells, resistance bands, and bodyweight exercises. Our trainer teaches you to move efficiently and safely in all planes of motion while building strength that translates to everyday tasks. The class is perfect for older adults, busy professionals, parents, or anyone who wants strength training that makes daily life easier. Regular participation improves balance, coordination, and functional capacity while reducing injury risk during daily activities.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(69, 3, 116, 'Women\'s Strength Training Bootcamp', 40.00, 60, 0, 'Empowering strength training class designed specifically for women, creating a supportive environment to build confidence and strength while dispelling myths about women and weightlifting. This class combines strength training with metabolic conditioning to build lean muscle, increase bone density, and boost metabolism. You\'ll learn that lifting weights won\'t make you bulky but will create a strong, toned physique and improve overall health. Our female trainer provides guidance on training during different life phases, addresses common concerns, and creates workout programs that fit busy lifestyles. The class covers proper nutrition for muscle building, body composition changes, and how strength training supports hormonal health. Perfect for women of all fitness levels who want to feel strong and confident. The supportive group environment encourages women to challenge themselves and achieve goals they never thought possible.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(70, 5, 116, 'Athletic Performance Training', 50.00, 75, 0, 'Sport-specific strength and conditioning program designed to enhance athletic performance through targeted training that improves power, speed, agility, and sport-specific strength. This advanced class incorporates plyometrics, Olympic lifting variations, and movement patterns that directly translate to improved athletic performance. You\'ll work on explosive power development, injury prevention protocols, and recovery strategies used by elite athletes. Our performance coach designs programs based on your specific sport and position requirements, addressing individual weaknesses and maximizing strengths. The class covers periodization for competitive seasons, testing and assessment protocols, and mental preparation techniques. Perfect for competitive athletes, weekend warriors, or anyone wanting to train like an athlete. The comprehensive approach addresses all aspects of performance including strength, power, speed, agility, and injury prevention for optimal athletic development.', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(71, 5, 117, 'Hatha Yoga for Beginners', 20.00, 75, 1, 'Gentle introduction to yoga focusing on basic postures, breathing techniques, and relaxation methods perfect for those new to yoga practice. This slow-paced class emphasizes proper alignment, flexibility development, and stress reduction through mindful movement and breath awareness. You\'ll learn fundamental poses, basic breathing exercises (pranayama), and meditation techniques that form the foundation of all yoga styles. Our experienced instructor provides detailed instruction and modifications to ensure everyone can participate safely regardless of fitness level or flexibility. The class creates a non-competitive, supportive environment where students can explore their bodies\' capabilities without judgment. Perfect for beginners, those with physical limitations, or anyone seeking a gentle approach to fitness and stress relief. Regular practice improves flexibility, strength, balance, and mental clarity while reducing stress and promoting overall well-being.', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(72, 2, 117, 'Power Vinyasa Flow', 30.00, 60, 0, 'Dynamic, challenging yoga class that combines strength-building poses with flowing movements synchronized with breath to create a moving meditation that builds heat and energy. This intermediate to advanced class features creative sequences, arm balances, and inversions that challenge both physical and mental strength. You\'ll move through flowing transitions that require focus, coordination, and stamina while building lean muscle and improving cardiovascular fitness. Our skilled instructor guides you through creative sequences that vary each class, keeping the practice fresh and engaging. The class emphasizes the connection between breath and movement, creating a meditative flow state that reduces stress while building physical strength. Perfect for those with yoga experience looking for a challenging workout that combines fitness with mindfulness. Regular practice develops strength, flexibility, balance, and mental resilience.', '/images/services/1748953512_Power Vinyasa Flow.jpg', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 09:25:12'),
(73, 1, 117, 'Prenatal Yoga', 25.00, 60, 0, 'Specialized yoga practice designed to support expectant mothers through the physical and emotional changes of pregnancy while preparing the body and mind for childbirth. This gentle class focuses on poses that are safe during pregnancy, breathing techniques for labor, and relaxation methods to reduce pregnancy-related stress and discomfort. You\'ll learn modifications for each trimester, poses that help with common pregnancy issues like back pain and swelling, and techniques to connect with your growing baby. Our prenatal-certified instructor creates a supportive community where expectant mothers can share experiences and concerns. The class includes pelvic floor strengthening, hip opening poses, and relaxation techniques that can be used during labor. Perfect for pregnant women at any stage who want to stay active safely and prepare for childbirth naturally. Regular practice can lead to easier labor, reduced pregnancy discomfort, and better emotional well-being.', '/images/services/1748953567_Prenatal Yoga.jpg', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 09:26:07'),
(74, 6, 117, 'Restorative Yoga and Deep Relaxation', 25.00, 75, 0, 'Deeply relaxing yoga practice using props and supported poses to promote complete physical and mental relaxation, stress relief, and nervous system restoration. This gentle class uses bolsters, blankets, and blocks to support the body in comfortable positions held for extended periods, allowing for deep release and healing. You\'ll experience the benefits of passive stretching, guided meditation, and breathing exercises designed to activate the parasympathetic nervous system and promote deep relaxation. Our instructor creates a peaceful, nurturing environment that supports letting go of tension and stress. The class is perfect for anyone dealing with stress, anxiety, insomnia, or chronic pain, as well as those who simply want to balance more active practices with deep restoration. Regular practice improves sleep quality, reduces stress hormones, and promotes overall healing and well-being.', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(75, 4, 117, 'Hot Yoga Challenge', 35.00, 90, 0, 'Intensive yoga practice performed in a heated room (95-105°F) that combines challenging poses with heat therapy to promote deep muscle relaxation, increased flexibility, and detoxification through sweating. This demanding class features a set sequence of poses designed to work every muscle, joint, and organ in the body while building mental discipline and focus. The heat allows for deeper stretching and helps prevent injury while promoting cardiovascular benefits similar to aerobic exercise. Our experienced hot yoga instructor guides you through proper hydration, breathing techniques, and safety protocols essential for practicing in heated conditions. The class builds mental toughness, physical strength, and flexibility while promoting detoxification and stress relief. Perfect for experienced yoga practitioners looking for an intense challenge or those who enjoy heat therapy benefits. Regular practice dramatically improves flexibility, strength, and mental resilience.', '/images/services/1748953599_Hot Yoga Challenge.jpg', 4.60, 1, 0, '2025-06-03 08:46:28', '2025-06-03 09:26:39'),
(76, 5, 118, 'Zumba Fitness Party', 18.00, 60, 1, 'High-energy dance fitness class that combines Latin and international music with fun, easy-to-follow dance moves to create an effective workout that feels like a party. This beginner-friendly class features a mix of salsa, merengue, cumbia, reggaeton, and other dance styles that get your heart pumping while improving coordination and rhythm. You\'ll burn calories, tone muscles, and boost cardiovascular fitness while having so much fun you\'ll forget you\'re exercising. Our certified Zumba instructor creates an inclusive, judgment-free environment where everyone can move at their own pace and style. The class welcomes all fitness levels and dance experience - no coordination required, just a willingness to move and have fun. Perfect for those who find traditional exercise boring or anyone who loves music and dancing. Regular participation improves cardiovascular health, coordination, and mood while providing an excellent calorie burn.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(77, 6, 118, 'Zumba Gold for Seniors', 15.00, 45, 1, 'Modified Zumba program designed specifically for older adults and beginners, featuring lower-impact movements and a slower pace while maintaining the fun, party atmosphere of traditional Zumba. This age-appropriate class focuses on balance, coordination, and cardiovascular health while accommodating physical limitations and varying fitness levels. You\'ll enjoy the same great music and dance styles but with movements that are easier on joints and more accessible for seniors. Our Zumba Gold certified instructor emphasizes safety, provides chair modifications when needed, and creates a supportive community atmosphere. The class improves balance, flexibility, and cognitive function while providing social interaction and mood enhancement. Perfect for active seniors, those with mobility limitations, or anyone preferring a gentler approach to dance fitness. Regular participation helps maintain independence, improves quality of life, and provides the joy of movement and music.', '/images/services/1748953635_Zumba Gold for Seniors.jpg', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 09:27:15'),
(78, 3, 118, 'Aqua Zumba Water Fitness', 22.00, 50, 0, 'Innovative water-based Zumba class that combines the fun of dance with the benefits of aquatic exercise, creating a low-impact, high-energy workout perfect for all fitness levels. The water provides natural resistance while supporting joints, making this class ideal for those with arthritis, injuries, or anyone who wants an effective workout without high impact stress. You\'ll enjoy the same great Zumba music and moves adapted for the pool environment, creating a unique and refreshing fitness experience. Our Aqua Zumba instructor leads you through choreography designed specifically for water, taking advantage of water\'s resistance and buoyancy properties. The class provides excellent cardiovascular conditioning, muscle toning, and flexibility improvement while being gentle on joints. Perfect for seniors, those recovering from injury, pregnant women, or anyone who loves water and wants to try something different. The cooling effect of water makes this an ideal summer workout.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(79, 6, 118, 'Zumba Toning with Weights', 25.00, 60, 0, 'Enhanced Zumba class that incorporates lightweight toning sticks (or dumbbells) to add resistance training to the dance party, creating a complete workout that builds lean muscle while burning calories. This intermediate class combines the cardiovascular benefits of Zumba with strength training elements to sculpt and tone the entire body. You\'ll use 1-2 pound weights during specific songs to target arms, core, and other muscle groups while maintaining the fun, dance-based format. Our instructor demonstrates proper form for weighted movements and provides modifications for different fitness levels. The class alternates between pure dance cardio and toning segments, creating variety and targeting different energy systems. Perfect for those who want to add strength training to their cardio routine or Zumba lovers looking to take their workout to the next level. Regular participation improves muscle definition, bone density, and overall body composition.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(80, 4, 118, 'Kids Zumba Dance Party', 12.00, 30, 0, 'Fun, energetic dance class designed specifically for children ages 4-12, combining age-appropriate music with simple dance moves to promote physical activity, coordination, and self-expression. This class helps kids develop a love for movement and music while improving motor skills, rhythm, and confidence. You\'ll see your child learn basic dance steps, follow directions, and express creativity through movement in a supportive, non-competitive environment. Our kid-friendly instructor uses games, props, and interactive activities to keep children engaged while sneaking in a great workout. The class promotes social skills, teamwork, and following instructions while building physical fitness and coordination. Perfect for active kids, those who love music and dancing, or parents looking for a fun way to get their children moving. Regular participation improves coordination, builds confidence, and establishes healthy exercise habits that can last a lifetime.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(81, 2, 120, 'Comprehensive Fertility Assessment', 150.00, 90, 1, 'Complete fertility evaluation including hormone testing, ovulation tracking, and reproductive health analysis to help women understand their fertility status and optimize conception chances. This comprehensive service includes detailed consultation with fertility specialists, review of menstrual cycle patterns, and personalized recommendations for improving fertility. You\'ll receive guidance on nutrition, lifestyle factors, and timing strategies that can enhance reproductive health. Our certified fertility counselors provide education about the menstrual cycle, ovulation signs, and optimal timing for conception. The assessment includes recommendations for fertility-friendly supplements, stress management techniques, and when to seek additional medical intervention. Perfect for women trying to conceive, those with irregular cycles, or anyone wanting to understand their reproductive health better. The service provides valuable insights and actionable steps to support your fertility journey.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(82, 3, 120, 'Ovulation Tracking and Cycle Optimization', 85.00, 60, 0, 'Personalized ovulation tracking service using advanced monitoring techniques to identify your most fertile days and optimize timing for conception. This service combines traditional fertility awareness methods with modern technology to provide accurate ovulation prediction. You\'ll learn to track basal body temperature, cervical mucus changes, and other fertility signs while using digital tools for enhanced accuracy. Our fertility specialists provide ongoing support and interpretation of your data, helping you understand your unique cycle patterns. The service includes education about fertility windows, lifestyle factors that affect ovulation, and strategies for maximizing conception chances. Perfect for women with irregular cycles, those who have been trying to conceive, or anyone wanting to understand their body\'s natural rhythms better. Regular monitoring helps identify potential issues early and provides valuable data for healthcare providers.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(83, 3, 120, 'Preconception Health Counseling', 120.00, 75, 0, 'Comprehensive preconception counseling service designed to optimize health and wellness before pregnancy, addressing nutrition, lifestyle, and medical factors that can impact fertility and pregnancy outcomes. This service includes detailed health assessment, nutritional analysis, and personalized recommendations for preparing your body for pregnancy. You\'ll receive guidance on prenatal vitamins, dietary changes, exercise recommendations, and lifestyle modifications that support fertility and healthy pregnancy. Our certified preconception counselors address concerns about age, previous pregnancy complications, and family history while providing evidence-based recommendations. The service covers topics like weight management, stress reduction, environmental toxin exposure, and partner health considerations. Perfect for women planning pregnancy, those with previous pregnancy complications, or couples wanting to optimize their health before conceiving. The comprehensive approach helps ensure the best possible start for both mother and baby.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(84, 1, 120, 'Male Fertility Support and Education', 100.00, 60, 0, 'Specialized fertility support service for men focusing on male reproductive health, lifestyle factors, and partner support during the conception journey. This service addresses the often-overlooked male component of fertility, providing education about sperm health, lifestyle factors that affect male fertility, and ways men can support their partners during fertility treatments. You\'ll receive guidance on nutrition, exercise, stress management, and environmental factors that impact sperm quality. Our male fertility specialists provide confidential consultation about concerns, testing recommendations, and lifestyle modifications that can improve fertility outcomes. The service includes partner communication strategies and emotional support for men navigating fertility challenges. Perfect for men wanting to optimize their fertility, those with known fertility issues, or partners supporting women through fertility treatments. The comprehensive approach recognizes that fertility is a shared responsibility and provides men with actionable steps to contribute positively to conception efforts.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(85, 6, 120, 'Fertility Technology and App Integration', 95.00, 45, 0, 'Modern fertility tracking service that integrates cutting-edge technology, apps, and wearable devices to provide comprehensive fertility monitoring and data analysis. This tech-savvy approach combines traditional fertility awareness with digital tools for enhanced accuracy and convenience. You\'ll learn to use fertility apps, wearable temperature monitors, and other digital tools while understanding how to interpret and act on the data they provide. Our tech-certified fertility counselors help you choose the right tools for your needs and integrate multiple data sources for comprehensive fertility tracking. The service includes troubleshooting technology issues, data interpretation, and recommendations for the most effective digital fertility tools. Perfect for tech-savvy women, busy professionals who need convenient tracking methods, or anyone wanting to leverage technology for fertility optimization. The modern approach makes fertility tracking more accessible and accurate while providing valuable data for healthcare providers.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.50, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(86, 4, 121, 'Comprehensive Menstrual Health Assessment', 80.00, 60, 1, 'Detailed evaluation of menstrual health patterns, symptoms, and overall reproductive wellness to identify potential issues and optimize menstrual cycle health. This comprehensive service includes analysis of cycle length, flow patterns, PMS symptoms, and associated health concerns. You\'ll receive personalized recommendations for managing menstrual symptoms, improving cycle regularity, and supporting overall reproductive health. Our certified menstrual health specialists provide education about normal vs. abnormal menstrual patterns and when to seek medical attention. The assessment covers lifestyle factors that affect menstrual health, including nutrition, exercise, stress, and sleep patterns. Perfect for women with irregular cycles, severe PMS, or anyone wanting to better understand their menstrual health. The service provides valuable insights for optimizing reproductive wellness and identifying potential health concerns early.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(87, 4, 121, 'PMS and PMDD Management Program', 110.00, 75, 0, 'Specialized program for managing premenstrual syndrome (PMS) and premenstrual dysphoric disorder (PMDD) through natural approaches, lifestyle modifications, and symptom tracking. This comprehensive service addresses the physical and emotional symptoms that can significantly impact quality of life during the premenstrual phase. You\'ll learn evidence-based strategies for managing mood changes, physical discomfort, and other PMS symptoms through nutrition, exercise, stress management, and targeted supplements. Our certified PMS specialists provide personalized treatment plans based on your specific symptoms and lifestyle. The program includes tracking tools to identify patterns and triggers, helping you anticipate and manage symptoms more effectively. Perfect for women experiencing severe PMS, those diagnosed with PMDD, or anyone wanting natural approaches to menstrual symptom management. The holistic approach addresses root causes while providing practical tools for immediate symptom relief.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(88, 5, 121, 'Menstrual Cycle Education and Empowerment', 65.00, 90, 0, 'Educational service designed to help women understand their menstrual cycles, normalize menstrual experiences, and develop a positive relationship with their reproductive health. This empowering program covers menstrual cycle physiology, hormonal changes throughout the cycle, and how these changes affect mood, energy, and overall well-being. You\'ll learn to work with your natural rhythms rather than against them, optimizing productivity and self-care based on cycle phases. Our certified menstrual educators provide evidence-based information while addressing cultural taboos and misconceptions about menstruation. The service includes practical guidance on menstrual products, pain management, and when to seek medical care. Perfect for young women beginning their menstrual journey, those wanting to better understand their cycles, or anyone seeking to develop a healthier relationship with their reproductive health. The educational approach promotes body literacy and menstrual confidence.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(89, 1, 121, 'Hormonal Balance Optimization', 125.00, 80, 0, 'Comprehensive service focusing on natural hormone balance through lifestyle modifications, nutrition, and targeted interventions to support healthy menstrual cycles and overall hormonal wellness. This holistic approach addresses the root causes of hormonal imbalances that can affect menstrual health, mood, energy, and overall well-being. You\'ll receive personalized recommendations for nutrition, exercise, stress management, and sleep optimization that support healthy hormone production and metabolism. Our certified hormone health specialists provide guidance on natural supplements, herbal remedies, and lifestyle practices that promote hormonal balance. The service includes education about how different life phases affect hormones and strategies for supporting hormonal health throughout these transitions. Perfect for women experiencing hormonal imbalances, irregular cycles, or those wanting to optimize their hormonal health naturally. The comprehensive approach addresses multiple factors that influence hormonal wellness for lasting results.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(90, 1, 121, 'Digital Menstrual Tracking Setup and Support', 55.00, 45, 0, 'Technology-focused service that helps women set up and optimize digital menstrual tracking tools, apps, and devices for comprehensive cycle monitoring and health insights. This modern approach to menstrual tracking combines convenience with accuracy, helping you gather valuable data about your reproductive health. You\'ll learn to use various tracking apps, understand which metrics are most important to monitor, and how to interpret the data for health insights. Our tech-savvy menstrual health specialists help you choose the right digital tools for your needs and integrate tracking into your daily routine. The service includes troubleshooting common issues, privacy considerations, and how to share data effectively with healthcare providers. Perfect for busy women who want convenient tracking methods, those interested in data-driven health insights, or anyone wanting to leverage technology for better menstrual health management. The digital approach makes tracking more accessible and provides valuable long-term health data.', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(91, 3, 126, 'Complete Bridal Makeup Package', 250.00, 120, 1, 'Comprehensive bridal makeup service that creates a flawless, long-lasting look for your special day, including trial session, wedding day application, and touch-up kit. This premium service begins with a detailed consultation to understand your vision, skin type, and wedding theme. You\'ll receive a complete trial session 2-4 weeks before your wedding to perfect the look and make any adjustments. Our certified bridal makeup artists use professional, high-quality products that photograph beautifully and last throughout your entire celebration. The service includes false lashes, contouring, highlighting, and airbrush foundation for a flawless finish. We provide a touch-up kit with lipstick and powder for the reception, plus detailed photos of your final look for future reference. Perfect for brides who want to look and feel absolutely stunning on their wedding day with makeup that complements their dress, venue, and personal style.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(92, 6, 126, 'Bridal Party Makeup Services', 85.00, 45, 0, 'Coordinated makeup services for the entire bridal party, creating cohesive looks that complement the bride while allowing each person\'s individual beauty to shine. This comprehensive service includes makeup for bridesmaids, mother of the bride, flower girls, and any other special members of the wedding party. Our team of professional makeup artists works efficiently to ensure everyone is ready on time while maintaining the highest quality standards. Each person receives a personalized consultation to determine the most flattering look that coordinates with the overall wedding aesthetic. We use long-wearing, photo-friendly products that look beautiful in person and in photographs. The service includes false lashes for those who want them, and we provide touch-up products for key members of the bridal party. Perfect for creating a cohesive, polished look for the entire wedding party while ensuring everyone feels confident and beautiful.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(93, 3, 126, 'Destination Wedding Makeup', 300.00, 150, 0, 'Specialized makeup service designed for destination weddings, taking into account climate, humidity, and travel considerations to ensure your makeup looks perfect regardless of location. This service includes detailed consultation about your destination\'s climate and venue to select appropriate products and techniques. We use waterproof, humidity-resistant, and long-wearing formulas that can withstand beach ceremonies, tropical climates, or mountain settings. The service includes a trial session using the exact products that will be used on your wedding day, plus detailed instructions for touch-ups and maintenance. We provide a comprehensive emergency kit with all necessary products for the duration of your trip. Our destination wedding specialists understand the unique challenges of different climates and venues, ensuring your makeup looks flawless from ceremony to reception regardless of weather conditions. Perfect for brides planning outdoor ceremonies, beach weddings, or celebrations in challenging climates.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(94, 4, 126, 'Vintage and Themed Bridal Makeup', 200.00, 90, 0, 'Specialized makeup service for themed weddings, creating authentic vintage looks or specific era-inspired makeup that perfectly complements your unique wedding theme. This creative service includes extensive research and consultation to ensure historical accuracy and authenticity for your chosen theme. Whether you\'re planning a 1920s Great Gatsby wedding, 1950s vintage celebration, or bohemian themed ceremony, our specialized artists have the expertise to create the perfect look. We use period-appropriate techniques and color palettes while ensuring the makeup photographs beautifully with modern cameras. The service includes a detailed trial session with multiple look options, historical context and styling tips, and coordination with your hair stylist for a cohesive vintage appearance. Perfect for couples planning themed weddings, vintage enthusiasts, or brides who want a unique, memorable look that tells a story and creates stunning photographs.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(95, 1, 126, 'Bridal Makeup Lessons and DIY Guidance', 150.00, 180, 0, 'Comprehensive makeup education service for brides who prefer to do their own wedding makeup, providing professional techniques, product recommendations, and hands-on training for a flawless DIY bridal look. This educational service includes detailed consultation about your skin type, face shape, and desired look, followed by step-by-step instruction in professional makeup techniques. You\'ll learn contouring, highlighting, eye makeup application, and long-wearing techniques that ensure your makeup lasts throughout your wedding day. Our professional makeup artists provide personalized product recommendations within your budget, application tips for photography, and troubleshooting guidance for common issues. The service includes practice sessions, detailed written instructions with photos, and a final trial run before your wedding. Perfect for budget-conscious brides, makeup enthusiasts who enjoy doing their own makeup, or brides who want the confidence and skills to create their own beautiful wedding look.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(96, 3, 127, 'Red Carpet Glamour Makeup', 120.00, 75, 1, 'Professional glamour makeup service designed for special events, galas, and red carpet occasions where you want to look absolutely stunning and camera-ready. This high-end service creates dramatic, sophisticated looks using professional techniques and premium products that photograph beautifully under any lighting. You\'ll receive a detailed consultation to understand the event, your outfit, and desired level of drama, followed by expert application that enhances your natural features while creating that coveted red carpet glow. Our celebrity makeup artists use contouring, highlighting, and color theory to create dimension and drama that looks flawless in person and in photographs. The service includes false lashes, precise lip application, and setting techniques that ensure your makeup lasts throughout the entire event. Perfect for galas, award ceremonies, milestone celebrations, or any special occasion where you want to feel like a celebrity and make a memorable impression.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(97, 2, 127, 'Corporate Event and Professional Makeup', 75.00, 45, 0, 'Polished, professional makeup service designed for business events, conferences, presentations, and corporate photography where you need to look confident and authoritative while maintaining a professional appearance. This service creates sophisticated looks that enhance your natural features without being distracting or overly dramatic. You\'ll receive makeup that photographs well under office lighting and video conferencing, with attention to color choices that complement business attire and professional settings. Our corporate makeup specialists understand the balance between looking polished and maintaining credibility in professional environments. The service includes techniques for looking refreshed and energetic, even during long conference days, plus guidance on touch-up products for maintaining your look throughout extended events. Perfect for executives, speakers, professionals attending important meetings, or anyone who wants to project confidence and competence through their appearance.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28');
INSERT INTO `services` (`id`, `branch_id`, `category_id`, `name`, `price`, `duration`, `featured`, `description`, `image`, `rating`, `is_available`, `home_service`, `created_at`, `updated_at`) VALUES
(98, 3, 127, 'Prom and Formal Dance Makeup', 65.00, 60, 0, 'Age-appropriate, stunning makeup service designed specifically for prom, homecoming, and other formal dance events, creating looks that are sophisticated yet suitable for young women. This service focuses on enhancing natural beauty while creating the glamour and excitement appropriate for these milestone events. You\'ll receive a consultation that considers your dress color, personal style, and comfort level with makeup, followed by expert application that creates a polished, age-appropriate look. Our specialists understand current trends while ensuring the makeup is suitable for photography and dancing throughout the evening. The service includes false lashes if desired, long-wearing formulas that withstand dancing and celebration, and touch-up guidance for maintaining the look throughout the event. Perfect for high school students attending formal dances, young women wanting to feel confident and beautiful at special events, or parents seeking professional, appropriate makeup for their daughters.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(99, 2, 127, 'Holiday and Seasonal Event Makeup', 80.00, 50, 0, 'Festive, themed makeup service for holiday parties, seasonal celebrations, and themed events that incorporates seasonal colors and trends while maintaining elegance and sophistication. This creative service adapts current makeup trends to complement holiday themes, whether you\'re attending a Christmas party, New Year\'s Eve celebration, Halloween event, or seasonal gathering. You\'ll receive makeup that incorporates appropriate seasonal elements like metallic accents for New Year\'s, warm tones for autumn events, or festive colors for holiday celebrations. Our seasonal makeup specialists stay current with trends while ensuring the look is appropriate for the specific event and your personal style. The service includes guidance on coordinating makeup with seasonal outfits and accessories, plus recommendations for maintaining the look throughout evening celebrations. Perfect for holiday party-goers, those attending themed events, or anyone wanting to embrace seasonal beauty trends while looking sophisticated and festive.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.60, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(100, 4, 127, 'Photography and Photoshoot Makeup', 95.00, 60, 0, 'Specialized makeup service designed specifically for photography sessions, headshots, and professional photoshoots, using techniques and products that ensure you look flawless under camera lights and in high-resolution images. This technical service requires understanding of how makeup translates to photography, including color theory, contouring for cameras, and products that don\'t create flashback or unwanted shine. You\'ll receive makeup that enhances your features for the camera while looking natural and polished in the final images. Our photography makeup specialists work with lighting conditions, understand different camera requirements, and use professional products specifically chosen for photographic work. The service includes consultation about the type of photography, intended use of images, and any specific requirements from the photographer. Perfect for professional headshots, modeling portfolios, family portraits, or any situation where high-quality photographs are the primary goal.', 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(101, 3, 134, 'Personalized Weight Management Program', 150.00, 90, 1, 'Comprehensive, science-based weight management program that creates a personalized nutrition and lifestyle plan tailored to your individual needs, preferences, and health goals. This holistic approach goes beyond simple calorie counting to address the complex factors that influence weight, including metabolism, hormones, lifestyle, and psychological relationship with food. You\'ll receive a detailed assessment of your current eating patterns, medical history, and lifestyle factors, followed by a customized plan that fits your schedule and preferences. Our registered dietitians provide ongoing support, meal planning assistance, and regular adjustments to ensure sustainable progress. The program includes education about nutrition science, portion control, mindful eating, and strategies for maintaining long-term success. Perfect for individuals wanting to lose weight sustainably, those who have struggled with yo-yo dieting, or anyone seeking a healthy, balanced approach to weight management that doesn\'t involve restrictive or extreme measures.', 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(102, 5, 134, 'Medical Nutrition Therapy', 130.00, 75, 0, 'Specialized nutrition counseling for individuals with medical conditions that require dietary management, including diabetes, heart disease, kidney disease, digestive disorders, and other chronic health conditions. This evidence-based approach uses nutrition as a therapeutic tool to manage symptoms, slow disease progression, and improve overall health outcomes. You\'ll work with a registered dietitian who specializes in medical nutrition therapy and understands the complex interactions between food, medications, and health conditions. The service includes detailed meal planning that considers your medical restrictions, medication timing, and treatment goals while ensuring nutritional adequacy and meal enjoyment. Our medical nutrition specialists coordinate with your healthcare team to ensure your nutrition plan supports your overall treatment plan. Perfect for individuals newly diagnosed with chronic conditions, those struggling to manage their condition through diet, or anyone wanting to optimize their nutrition for better health outcomes and disease management.', 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(103, 1, 134, 'Plant-Based Nutrition Transition', 110.00, 60, 0, 'Comprehensive guidance for transitioning to a plant-based diet safely and sustainably, ensuring nutritional adequacy while exploring the health, environmental, and ethical benefits of plant-based eating. This specialized service addresses common concerns about protein, vitamins, minerals, and meal planning while making the transition enjoyable and sustainable. You\'ll receive education about plant-based nutrition science, meal planning strategies, shopping guides, and cooking techniques that make plant-based eating delicious and satisfying. Our plant-based nutrition specialists help you navigate social situations, dining out, and family meal planning while ensuring you meet all nutritional needs. The program includes gradual transition strategies, recipe suggestions, and ongoing support to help you maintain your new eating pattern long-term. Perfect for individuals interested in plant-based eating for health reasons, those concerned about environmental impact, or anyone wanting to reduce animal product consumption while maintaining optimal nutrition and meal satisfaction.', 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(104, 4, 134, 'Sports Nutrition and Performance Optimization', 125.00, 70, 0, 'Specialized nutrition counseling for athletes and active individuals focused on optimizing performance, recovery, and body composition through strategic nutrition timing and food choices. This performance-focused approach considers your training schedule, sport-specific demands, and individual goals to create a nutrition plan that enhances athletic performance. You\'ll learn about pre-workout nutrition, post-workout recovery, hydration strategies, and competition day fueling that maximizes your athletic potential. Our sports nutrition specialists understand the unique nutritional needs of different sports and training phases, providing guidance on supplements, meal timing, and body composition goals. The service includes practical strategies for meal prep, travel nutrition, and managing nutrition during different training seasons. Perfect for competitive athletes, weekend warriors, fitness enthusiasts, or anyone wanting to optimize their nutrition for better workout performance, faster recovery, and improved body composition while maintaining overall health and energy.', 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(105, 6, 134, 'Intuitive Eating and Mindful Nutrition', 120.00, 60, 0, 'Revolutionary approach to nutrition that helps you develop a healthy, sustainable relationship with food by learning to trust your body\'s natural hunger and fullness cues while rejecting diet culture and food restrictions. This anti-diet approach focuses on healing your relationship with food, body image, and eating behaviors rather than pursuing weight loss or following external food rules. You\'ll learn to distinguish between physical and emotional hunger, practice mindful eating techniques, and develop body neutrality and acceptance. Our intuitive eating counselors are trained in Health at Every Size principles and help you unlearn diet mentality while rediscovering the joy and satisfaction of eating. The approach addresses emotional eating, food guilt, and the psychological aspects of eating while promoting overall well-being regardless of weight. Perfect for individuals recovering from disordered eating, those tired of diet culture, chronic dieters wanting to break the cycle, or anyone seeking a peaceful, sustainable relationship with food and their body.', 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(106, 3, 147, 'Cognitive Behavioral Therapy (CBT)', 120.00, 50, 1, 'Evidence-based individual therapy focusing on identifying and changing negative thought patterns and behaviors that contribute to emotional distress and mental health challenges. CBT is a structured, goal-oriented approach that helps you develop practical coping strategies and problem-solving skills for managing anxiety, depression, trauma, and other mental health concerns. You\'ll work with a licensed therapist to understand the connection between thoughts, feelings, and behaviors, learning to recognize and challenge unhelpful thinking patterns. Sessions include homework assignments, skill-building exercises, and practical tools you can use in daily life. Our CBT specialists are trained in the latest evidence-based techniques and tailor the approach to your specific needs and goals. Perfect for individuals dealing with anxiety disorders, depression, PTSD, OCD, or anyone wanting to develop better emotional regulation and coping skills. The structured approach provides measurable progress and lasting tools for mental wellness.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(107, 2, 147, 'Trauma-Informed Therapy and EMDR', 140.00, 60, 0, 'Specialized therapy for individuals who have experienced trauma, using evidence-based approaches including Eye Movement Desensitization and Reprocessing (EMDR) to help process and heal from traumatic experiences. This gentle yet effective approach helps your brain process traumatic memories in a way that reduces their emotional impact and allows for healing. You\'ll work with a trauma-specialized therapist who understands the complex effects of trauma on the mind and body. Sessions are conducted at your pace with careful attention to safety and stabilization before processing work begins. Our trauma therapists are trained in multiple modalities including EMDR, somatic approaches, and trauma-focused CBT. The therapy helps reduce symptoms of PTSD, anxiety, depression, and other trauma-related conditions while building resilience and post-traumatic growth. Perfect for survivors of abuse, accidents, military trauma, or any overwhelming life experiences that continue to impact daily functioning.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(108, 3, 147, 'Mindfulness-Based Therapy', 110.00, 55, 0, 'Integrative therapy approach that combines traditional talk therapy with mindfulness meditation and awareness practices to help you develop a healthier relationship with thoughts, emotions, and life experiences. This holistic approach teaches you to observe your thoughts and feelings without judgment, reducing reactivity and increasing emotional regulation. You\'ll learn practical mindfulness techniques that can be used in daily life to manage stress, anxiety, and difficult emotions. Sessions include guided meditations, breathing exercises, and mindful awareness practices alongside traditional therapeutic conversation. Our mindfulness-trained therapists help you develop present-moment awareness and acceptance while working through specific mental health concerns. The approach is particularly effective for anxiety, depression, chronic pain, and stress-related conditions. Perfect for individuals interested in holistic approaches to mental health, those who want to develop meditation skills, or anyone seeking to cultivate greater peace and emotional balance in their lives.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.70, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(109, 3, 147, 'Life Transitions and Adjustment Counseling', 100.00, 50, 0, 'Supportive therapy designed to help individuals navigate major life changes, transitions, and adjustment challenges with greater ease and resilience. Life transitions can be overwhelming even when they\'re positive, and this specialized counseling provides tools and support for managing change effectively. You\'ll work with a therapist who understands the psychological impact of transitions and can help you process the emotions, fears, and excitement that come with change. Sessions focus on developing coping strategies, building resilience, and finding meaning and opportunity within challenging circumstances. Our transition specialists help with career changes, relationship changes, loss and grief, relocation, retirement, parenthood, and other major life shifts. The therapy provides a safe space to explore your feelings about change while developing practical skills for adaptation. Perfect for anyone facing major life transitions, those feeling stuck or overwhelmed by change, or individuals wanting to approach life transitions with greater confidence and clarity.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.80, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28'),
(110, 1, 147, 'Anxiety and Stress Management Therapy', 115.00, 50, 0, 'Specialized therapy focused specifically on understanding and managing anxiety disorders, panic attacks, and chronic stress through evidence-based techniques and personalized coping strategies. This targeted approach helps you understand the root causes of your anxiety while developing practical tools for managing symptoms and preventing escalation. You\'ll learn relaxation techniques, breathing exercises, cognitive restructuring, and exposure therapy methods tailored to your specific anxiety triggers. Sessions include education about the anxiety response, identification of personal triggers, and development of a comprehensive anxiety management plan. Our anxiety specialists are trained in the latest research and techniques for treating various anxiety disorders including generalized anxiety, social anxiety, panic disorder, and specific phobias. The therapy provides both immediate relief strategies and long-term tools for maintaining emotional balance. Perfect for individuals struggling with anxiety disorders, those experiencing chronic stress, or anyone wanting to develop better stress management skills for improved quality of life.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 4.90, 1, 0, '2025-06-03 08:46:28', '2025-06-03 08:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `service_specifications`
--

CREATE TABLE `service_specifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_specification_templates`
--

CREATE TABLE `service_specification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `awb_number` varchar(255) DEFAULT NULL COMMENT 'Aramex airwaybill / tracking number',
  `status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT 'Shipment status (e.g. pending, in_transit, delivered)',
  `shipment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional shipment details from Aramex' CHECK (json_valid(`shipment_details`)),
  `tracking_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Tracking history from Aramex' CHECK (json_valid(`tracking_history`)),
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `size_categories`
--

CREATE TABLE `size_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `size_categories`
--

INSERT INTO `size_categories` (`id`, `name`, `display_name`, `description`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'clothes', 'Clothes', 'Clothing sizes from XXS to 5XL with symbol representations', 1, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(2, 'shoes', 'Shoes', 'EU shoe sizes from 16 to 48 with foot length mappings', 1, 2, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(3, 'hats', 'Hats', 'EU hat sizes from 40 to 64 with age group mappings', 1, 3, '2025-05-29 20:44:29', '2025-05-29 20:44:29');

-- --------------------------------------------------------

--
-- Table structure for table `standardized_sizes`
--

CREATE TABLE `standardized_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `size_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `additional_info` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `standardized_sizes`
--

INSERT INTO `standardized_sizes` (`id`, `size_category_id`, `name`, `value`, `additional_info`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'XXS', 'Extra Extra Small', NULL, 1, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(2, 1, 'XS', 'Extra Small', NULL, 2, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(3, 1, 'S', 'Small', NULL, 3, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(4, 1, 'M', 'Medium', NULL, 4, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(5, 1, 'L', 'Large', NULL, 5, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(6, 1, 'XL', 'Extra Large', NULL, 6, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(7, 1, 'XXL', 'Extra Extra Large', NULL, 7, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(8, 1, '3XL', 'Triple Extra Large', NULL, 8, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(9, 1, '4XL', 'Quadruple Extra Large', NULL, 9, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(10, 1, '5XL', 'Quintuple Extra Large', NULL, 10, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(11, 2, '16', 'EU 16', '9.7cm', 1, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(12, 2, '17', 'EU 17', '10.4cm', 2, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(13, 2, '18', 'EU 18', '11.0cm', 3, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(14, 2, '19', 'EU 19', '11.7cm', 4, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(15, 2, '20', 'EU 20', '12.3cm', 5, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(16, 2, '21', 'EU 21', '13.0cm', 6, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(17, 2, '22', 'EU 22', '13.7cm', 7, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(18, 2, '23', 'EU 23', '14.3cm', 8, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(19, 2, '24', 'EU 24', '15.0cm', 9, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(20, 2, '25', 'EU 25', '15.7cm', 10, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(21, 2, '26', 'EU 26', '16.3cm', 11, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(22, 2, '27', 'EU 27', '17.0cm', 12, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(23, 2, '28', 'EU 28', '17.7cm', 13, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(24, 2, '29', 'EU 29', '18.3cm', 14, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(25, 2, '30', 'EU 30', '19.0cm', 15, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(26, 2, '31', 'EU 31', '19.7cm', 16, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(27, 2, '32', 'EU 32', '20.3cm', 17, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(28, 2, '33', 'EU 33', '21.0cm', 18, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(29, 2, '34', 'EU 34', '21.7cm', 19, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(30, 2, '35', 'EU 35', '22.5cm', 20, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(31, 2, '36', 'EU 36', '23.0cm', 21, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(32, 2, '37', 'EU 37', '23.5cm', 22, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(33, 2, '38', 'EU 38', '24.0cm', 23, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(34, 2, '39', 'EU 39', '24.5cm', 24, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(35, 2, '40', 'EU 40', '25.0cm', 25, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(36, 2, '41', 'EU 41', '25.5cm', 26, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(37, 2, '42', 'EU 42', '26.0cm', 27, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(38, 2, '43', 'EU 43', '26.5cm', 28, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(39, 2, '44', 'EU 44', '27.0cm', 29, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(40, 2, '45', 'EU 45', '27.5cm', 30, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(41, 2, '46', 'EU 46', '28.0cm', 31, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(42, 2, '47', 'EU 47', '28.5cm', 32, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(43, 2, '48', 'EU 48', '29.0cm', 33, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(44, 3, '40', 'EU 40', 'Newborn (0-3 months)', 1, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(45, 3, '42', 'EU 42', 'Baby (3-6 months)', 2, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(46, 3, '44', 'EU 44', 'Baby (6-12 months)', 3, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(47, 3, '46', 'EU 46', 'Toddler (1-2 years)', 4, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(48, 3, '48', 'EU 48', 'Toddler (2-3 years)', 5, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(49, 3, '50', 'EU 50', 'Child (3-5 years)', 6, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(50, 3, '52', 'EU 52', 'Child (5-8 years)', 7, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(51, 3, '54', 'EU 54', 'Child (8-12 years)', 8, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(52, 3, '56', 'EU 56', 'Teen/Adult Small', 9, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(53, 3, '57', 'EU 57', 'Adult Small', 10, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(54, 3, '58', 'EU 58', 'Adult Medium', 11, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(55, 3, '59', 'EU 59', 'Adult Medium-Large', 12, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(56, 3, '60', 'EU 60', 'Adult Large', 13, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(57, 3, '61', 'EU 61', 'Adult Large', 14, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(58, 3, '62', 'EU 62', 'Adult Extra Large', 15, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(59, 3, '63', 'EU 63', 'Adult Extra Large', 16, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29'),
(60, 3, '64', 'EU 64', 'Adult XXL', 17, 1, '2025-05-29 20:44:29', '2025-05-29 20:44:29');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `phone` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_ratings` int(11) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone`, `profile_image`, `status`, `average_rating`, `total_ratings`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', 'admin', '1234567890', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:02', '$2y$12$6LqqFrOWo4HCGEjD01TId.L7PhQogX2B5QtQq90s0htNVu6jctIn6', NULL, '2025-06-03 03:19:02', '2025-06-03 03:19:02'),
(2, 'John Vendor', 'john@vendor.com', 'vendor', '2345678901', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:04', '$2y$12$2/y/0QlZOBlkeflS3rthvuPoIxKRBxQGDqTkkw/7ArAeIf.byJgqm', NULL, '2025-06-03 03:19:07', '2025-06-03 03:19:07'),
(3, 'Jane Vendor', 'jane@vendor.com', 'vendor', '3456789012', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:05', '$2y$12$.h/9r8gbbl0FVPAwi589BO3saxOuUqQXZ22h0rSEUFq07.EapJUJ.', NULL, '2025-06-03 03:19:07', '2025-06-03 03:19:07'),
(4, 'Mike Vendor', 'mike@vendor.com', 'vendor', '4567890123', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:07', '$2y$12$JQ53kxbAq.NZPEJ7Erv1k.mu.9tWlFnIOzQsdWihwP2Q.06//BWle', NULL, '2025-06-03 03:19:07', '2025-06-03 03:19:07'),
(5, 'Alice Customer', 'alice@customer.com', 'customer', '5678901234', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:08', '$2y$12$v52i7Ky6UJvlo5ITcZO5COhKjclTai08r12N5O1YHC88dX/n1YRi6', NULL, '2025-06-03 03:19:12', '2025-06-03 03:19:12'),
(6, 'Bob Customer', 'bob@customer.com', 'customer', '6789012345', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:10', '$2y$12$cLXgL3CWmrT6QYA5Ye0IJuwLp3EhYOtGSUwtV92o.u3FvQ6wDyIb6', NULL, '2025-06-03 03:19:12', '2025-06-03 03:19:12'),
(7, 'Charlie Customer', 'charlie@customer.com', 'customer', '7890123456', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:11', '$2y$12$7jVFp4yvbuBs35ksK6gI0O0sRlcRwAm2okNhg/wG.Karzf6i7S1Su', NULL, '2025-06-03 03:19:12', '2025-06-03 03:19:12'),
(8, 'Diana Customer', 'diana@customer.com', 'customer', '8901234567', '/images/placeholder.jpg', 'active', 0.00, 0, '2025-06-03 03:19:12', '$2y$12$PaxFQAnuTaiNeOvHMsn8o.KC4ew8Fs5Z0suRuVWMqvej8vZ4acpaS', NULL, '2025-06-03 03:19:12', '2025-06-03 03:19:12'),
(9, 'Vendor User', 'vendor@example.com', 'vendor', NULL, NULL, 'active', 0.00, 0, NULL, '$2y$12$iDW3DsKDJS2ig4qet2wSheawXoFI7UYFoy9VyJj8JWfw85d5827eK', NULL, '2025-06-03 13:36:01', '2025-06-03 13:36:01'),
(10, 'Customer User', 'customer@example.com', 'customer', NULL, NULL, 'active', 0.00, 0, NULL, '$2y$12$tC3cCm9WbYVUXRYAEkTu/OpuVgwXNhxhWRpYDKAHoAyJ8hhgza8V2', NULL, '2025-06-03 13:36:01', '2025-06-03 13:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
--

CREATE TABLE `user_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `emirate` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_locations`
--

INSERT INTO `user_locations` (`id`, `user_id`, `name`, `emirate`, `latitude`, `longitude`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 4, 'Home', 'Sharjah', 25.37510900, 55.40424740, 1, '2025-05-24 09:45:11', '2025-05-24 09:45:11'),
(2, 4, 'Work', 'Sharjah', 25.38035900, 55.41009039, 0, '2025-05-24 09:46:40', '2025-05-24 09:47:01'),
(3, 11, 'bibi', 'Dubai', 25.20525179, 55.24008796, 1, '2025-05-25 19:04:09', '2025-05-25 19:04:09');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_order_statuses`
--

CREATE TABLE `vendor_order_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Status of this vendor''s portion of the order',
  `notes` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_order_statuses`
--

INSERT INTO `vendor_order_statuses` (`id`, `order_id`, `vendor_id`, `status`, `notes`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 17, 3, 'shipped', 'Order placed from mobile app', 4, '2025-05-28 18:27:28', '2025-05-28 18:27:28'),
(2, 16, 3, 'delivered', 'Order placed from mobile app', 4, '2025-05-28 18:27:55', '2025-05-28 18:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_order_status_history`
--

CREATE TABLE `vendor_order_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_order_status_history`
--

INSERT INTO `vendor_order_status_history` (`id`, `order_id`, `vendor_id`, `status`, `previous_status`, `notes`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 17, 3, 'shipped', NULL, 'Order placed from mobile app', 4, '2025-05-28 18:27:28', '2025-05-28 18:27:28'),
(2, 16, 3, 'delivered', NULL, 'Order placed from mobile app', 4, '2025-05-28 18:27:55', '2025-05-28 18:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_ratings`
--

CREATE TABLE `vendor_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5',
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_ratings`
--

INSERT INTO `vendor_ratings` (`id`, `customer_id`, `vendor_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(1, 11, 4, 4, 'good book', '2025-05-25 17:25:25', '2025-05-25 17:40:50'),
(2, 11, 2, 5, 'so far so good', '2025-05-25 22:25:03', '2025-05-25 22:25:03');

-- --------------------------------------------------------

--
-- Table structure for table `view_tracking`
--

CREATE TABLE `view_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `device_fingerprint` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_booking_number_unique` (`booking_number`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_service_id_foreign` (`service_id`),
  ADD KEY `bookings_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branches_user_id_foreign` (`user_id`),
  ADD KEY `branches_company_id_foreign` (`company_id`),
  ADD KEY `branches_popularity_score_index` (`popularity_score`),
  ADD KEY `branches_average_rating_index` (`average_rating`);

--
-- Indexes for table `branch_ratings`
--
ALTER TABLE `branch_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_ratings_customer_id_branch_id_unique` (`customer_id`,`branch_id`),
  ADD KEY `branch_ratings_branch_id_rating_index` (`branch_id`,`rating`),
  ADD KEY `branch_ratings_customer_id_index` (`customer_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`),
  ADD KEY `categories_trending_score_index` (`trending_score`),
  ADD KEY `categories_default_size_category_id_foreign` (`default_size_category_id`),
  ADD KEY `categories_type_default_size_category_id_index` (`type`,`default_size_category_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `companies_user_id_foreign` (`user_id`),
  ADD KEY `companies_vendor_score_index` (`vendor_score`);

--
-- Indexes for table `deals`
--
ALTER TABLE `deals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deals_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gift_options`
--
ALTER TABLE `gift_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gift_options_order_item_id_foreign` (`order_item_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_vendor_id_index` (`vendor_id`);

--
-- Indexes for table `order_item_options`
--
ALTER TABLE `order_item_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_options_order_item_id_foreign` (`order_item_id`);

--
-- Indexes for table `order_item_status_history`
--
ALTER TABLE `order_item_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_status_history_order_item_id_foreign` (`order_item_id`),
  ADD KEY `order_item_status_history_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_status_history_order_id_foreign` (`order_id`),
  ADD KEY `order_status_history_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_methods_user_id_foreign` (`user_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_transactions_transaction_uuid_unique` (`transaction_uuid`),
  ADD KEY `payment_transactions_user_id_foreign` (`user_id`),
  ADD KEY `payment_transactions_payment_method_id_foreign` (`payment_method_id`),
  ADD KEY `payment_transactions_payout_method_id_foreign` (`payout_method_id`);

--
-- Indexes for table `payout_methods`
--
ALTER TABLE `payout_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payout_methods_user_id_foreign` (`user_id`);

--
-- Indexes for table `payout_preferences`
--
ALTER TABLE `payout_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payout_preferences_user_id_foreign` (`user_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_user_id_foreign` (`user_id`),
  ADD KEY `products_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `product_branches`
--
ALTER TABLE `product_branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_branches_product_id_branch_id_unique` (`product_id`,`branch_id`),
  ADD KEY `product_branches_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_colors_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_color_sizes`
--
ALTER TABLE `product_color_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_color_size_combination` (`product_color_id`,`product_size_id`),
  ADD KEY `product_color_sizes_product_size_id_foreign` (`product_size_id`),
  ADD KEY `product_color_sizes_product_id_product_color_id_index` (`product_id`,`product_color_id`),
  ADD KEY `product_color_sizes_product_id_product_size_id_index` (`product_id`,`product_size_id`);

--
-- Indexes for table `product_option_types`
--
ALTER TABLE `product_option_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_option_types_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_option_values`
--
ALTER TABLE `product_option_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_option_values_option_type_id_foreign` (`option_type_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_sizes_size_category_id_foreign` (`size_category_id`),
  ADD KEY `product_sizes_standardized_size_id_foreign` (`standardized_size_id`),
  ADD KEY `product_sizes_product_id_size_category_id_index` (`product_id`,`size_category_id`);

--
-- Indexes for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_specifications_product_id_foreign` (`product_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `providers_user_id_foreign` (`user_id`),
  ADD KEY `providers_average_rating_index` (`average_rating`),
  ADD KEY `providers_provider_score_index` (`provider_score`);

--
-- Indexes for table `provider_locations`
--
ALTER TABLE `provider_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_locations_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `provider_products`
--
ALTER TABLE `provider_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_products_provider_id_product_id_unique` (`provider_id`,`product_id`),
  ADD KEY `provider_products_branch_id_foreign` (`branch_id`),
  ADD KEY `provider_products_category_id_foreign` (`category_id`),
  ADD KEY `provider_products_product_id_foreign` (`product_id`);

--
-- Indexes for table `provider_profiles`
--
ALTER TABLE `provider_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_profiles_category_id_foreign` (`category_id`),
  ADD KEY `provider_profiles_user_id_foreign` (`user_id`),
  ADD KEY `provider_profiles_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `provider_ratings`
--
ALTER TABLE `provider_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_ratings_vendor_id_provider_id_unique` (`vendor_id`,`provider_id`),
  ADD KEY `provider_ratings_provider_id_rating_index` (`provider_id`,`rating`),
  ADD KEY `provider_ratings_vendor_id_index` (`vendor_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_reviewable_type_reviewable_id_index` (`reviewable_type`,`reviewable_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_branch_id_foreign` (`branch_id`),
  ADD KEY `services_category_id_foreign` (`category_id`);

--
-- Indexes for table `service_specifications`
--
ALTER TABLE `service_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_specifications_service_id_foreign` (`service_id`);

--
-- Indexes for table `service_specification_templates`
--
ALTER TABLE `service_specification_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_specification_templates_category_id_foreign` (`category_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipments_order_id_foreign` (`order_id`);

--
-- Indexes for table `size_categories`
--
ALTER TABLE `size_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `size_categories_name_unique` (`name`);

--
-- Indexes for table `standardized_sizes`
--
ALTER TABLE `standardized_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `standardized_sizes_size_category_id_name_unique` (`size_category_id`,`name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_average_rating_index` (`role`,`average_rating`);

--
-- Indexes for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_locations_user_id_foreign` (`user_id`);

--
-- Indexes for table `vendor_order_statuses`
--
ALTER TABLE `vendor_order_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_order_statuses_order_id_vendor_id_unique` (`order_id`,`vendor_id`),
  ADD KEY `vendor_order_statuses_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_order_statuses_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `vendor_order_status_history`
--
ALTER TABLE `vendor_order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_order_status_history_order_id_foreign` (`order_id`),
  ADD KEY `vendor_order_status_history_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_order_status_history_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `vendor_ratings`
--
ALTER TABLE `vendor_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_ratings_customer_id_vendor_id_unique` (`customer_id`,`vendor_id`),
  ADD KEY `vendor_ratings_vendor_id_rating_index` (`vendor_id`,`rating`),
  ADD KEY `vendor_ratings_customer_id_index` (`customer_id`);

--
-- Indexes for table `view_tracking`
--
ALTER TABLE `view_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `view_tracking_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  ADD KEY `view_tracking_user_id_entity_type_entity_id_index` (`user_id`,`entity_type`,`entity_id`),
  ADD KEY `view_tracking_session_id_entity_type_entity_id_index` (`session_id`,`entity_type`,`entity_id`),
  ADD KEY `view_tracking_device_fingerprint_entity_type_entity_id_index` (`device_fingerprint`,`entity_type`,`entity_id`),
  ADD KEY `view_tracking_ip_address_entity_type_entity_id_index` (`ip_address`,`entity_type`,`entity_id`),
  ADD KEY `view_tracking_viewed_at_index` (`viewed_at`),
  ADD KEY `view_tracking_entity_type_entity_id_user_id_viewed_at_index` (`entity_type`,`entity_id`,`user_id`,`viewed_at`),
  ADD KEY `view_tracking_entity_type_entity_id_session_id_viewed_at_index` (`entity_type`,`entity_id`,`session_id`,`viewed_at`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `branch_ratings`
--
ALTER TABLE `branch_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deals`
--
ALTER TABLE `deals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gift_options`
--
ALTER TABLE `gift_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_item_options`
--
ALTER TABLE `order_item_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item_status_history`
--
ALTER TABLE `order_item_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payout_methods`
--
ALTER TABLE `payout_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payout_preferences`
--
ALTER TABLE `payout_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=381;

--
-- AUTO_INCREMENT for table `product_branches`
--
ALTER TABLE `product_branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product_color_sizes`
--
ALTER TABLE `product_color_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `product_option_types`
--
ALTER TABLE `product_option_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_option_values`
--
ALTER TABLE `product_option_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_specifications`
--
ALTER TABLE `product_specifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `provider_locations`
--
ALTER TABLE `provider_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `provider_products`
--
ALTER TABLE `provider_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `provider_profiles`
--
ALTER TABLE `provider_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `provider_ratings`
--
ALTER TABLE `provider_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `service_specifications`
--
ALTER TABLE `service_specifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_specification_templates`
--
ALTER TABLE `service_specification_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `size_categories`
--
ALTER TABLE `size_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `standardized_sizes`
--
ALTER TABLE `standardized_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_locations`
--
ALTER TABLE `user_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor_order_statuses`
--
ALTER TABLE `vendor_order_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_order_status_history`
--
ALTER TABLE `vendor_order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_ratings`
--
ALTER TABLE `vendor_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `view_tracking`
--
ALTER TABLE `view_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `branches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_default_size_category_id_foreign` FOREIGN KEY (`default_size_category_id`) REFERENCES `size_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
