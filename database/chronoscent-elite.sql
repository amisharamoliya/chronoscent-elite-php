-- ChronoScent Elite SQL Database Dump
-- Generated on 2026-10-27 15:29:28
SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `admins`
LOCK TABLES `admins` WRITE;
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$OEFAcPclg8PdWucs6QCJSevMLtTE9YmSxonRBFZBHonLhszG4H.ba');
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `brands`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` enum('Watch','Perfume','Both') NOT NULL DEFAULT 'Watch',
  `origin` varchar(150) DEFAULT 'Europe',
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `brands`
LOCK TABLES `brands` WRITE;
INSERT INTO `brands` (`id`, `name`, `category`, `origin`, `description`, `logo`, `created_at`) VALUES
(1, 'Rolex', 'Watch', 'Geneva, Switzerland', 'Pioneering Swiss luxury horology and timeless precision timepieces.', 'assets/images/brands/rolex.jpg', '2026-10-21 15:40:54'),
(2, 'Omega', 'Watch', 'Biel, Switzerland', 'Master chronometers and legendary official Olympic & moon landing horology.', 'assets/images/brands/omega.jpg', '2026-10-21 15:40:54'),
(3, 'Cartier', 'Watch', 'Paris, France', 'The jeweller of kings, iconic shaped watch cases and high Parisian elegance.', 'assets/images/brands/cartier.jpg', '2026-10-21 15:40:54'),
(4, 'Gucci', 'Watch', 'Florence, Italy', 'Italian contemporary craftsmanship with Swiss high-precision movements.', 'assets/images/brands/gucci.jpg', '2026-10-21 15:40:54'),
(5, 'Hublot', 'Watch', 'Nyon, Switzerland', 'Pioneering the Art of Fusion with innovative materials and bold designs.', 'assets/images/brands/hublot.jpg', '2026-10-21 15:40:54'),
(6, 'Chanel', 'Perfume', 'Paris, France', 'Timeless French luxury haute parfumerie with iconic olfactive masterpieces.', 'assets/images/brands/chanel.jpg', '2026-10-21 15:40:54'),
(7, 'Dior', 'Perfume', 'Paris, France', 'Radically fresh, sophisticated compositions of French couture perfumery.', 'assets/images/brands/dior.jpg', '2026-10-21 15:40:54'),
(8, 'Creed', 'Perfume', 'Paris, France', 'Seven generations of royal heritage hand-blended artisanal essences.', 'assets/images/brands/creed.jpg', '2026-10-21 15:40:54'),
(9, 'Versace', 'Perfume', 'Milan, Italy', 'Bold, glamorous, and sensual fragrances celebrating Italian luxury.', 'assets/images/brands/versace.jpg', '2026-10-21 15:40:55'),
(10, 'Yves Saint Laurent', 'Perfume', 'Paris, France', 'Parisian couture perfumes blending rebellious passion with timeless elegance.', 'assets/images/brands/ysl.jpg', '2026-10-21 15:40:55');
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('Watch','Perfume','General') NOT NULL DEFAULT 'General',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'bi-tag',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `categories`
LOCK TABLES `categories` WRITE;
INSERT INTO `categories` (`id`, `name`, `type`, `description`, `image`, `icon`, `created_at`) VALUES
(1, 'Watch', 'Watch', 'Precision Swiss and European automatic, mechanical, and chronograph timepieces from heritage watchmakers.', 'assets/images/cat_watch.png', 'bi-clock-history', '2026-10-22 14:36:11'),
(2, 'Perfume', 'Perfume', 'Artisanal Eau de Parfum, Extrait de Parfum, and couture essences crafted by revered Parisian and European fragrance houses.', 'assets/images/cat_perfume.png', 'bi-flower1', '2026-10-22 14:36:11'),
(4, 'Classic', 'Watch', 'Timeless, elegant, and sophisticated heritage designs crafted with traditional precision.', 'assets/images/watches/cartier-ballon-bleu-de-cartier.jpg', 'bi-award', '2026-10-22 16:48:31'),
(5, 'Sports', 'Watch', 'High-performance chronographs, professional diving timepieces, and durable luxury sports watches.', 'assets/images/watches/Hublot_Big_Bang.jpg', 'bi-speedometer2', '2026-10-22 16:48:32'),
(6, 'Modern', 'Perfume', 'Contemporary, fresh, and avant-garde luxury fragrance formulations for modern connoisseurs.', 'assets/images/perfumes/YSL-Y-Le-Parfum.jpg', 'bi-gem', '2026-10-22 16:48:32'),
(7, 'Signature', 'Perfume', 'Rare, artisanal olfactive creations and exclusive haute parfumerie defining unforgettable prestige.', 'assets/images/perfumes/CREED-AVENTUS-Eau-de-Parfum.jpg', 'bi-stars', '2026-10-22 16:48:32'),
(9, 'Diver', 'Watch', 'Professional diving and high-depth water-resistant timepieces engineered for oceanic adventures.', 'assets/images/watches/omega-sea-master.jpg', 'bi-water', '2026-10-27 19:15:26'),
(10, 'Dress', 'Watch', 'Slim, understated, and exquisitely crafted luxury timepieces designed for formal and black-tie elegance.', 'assets/images/watches/cartier-tank.jpg', 'bi-suit-club', '2026-10-27 19:15:26'),
(11, 'Floral', 'Perfume', 'Delicate, romantic, and blooming floral bouquets featuring rose, jasmine, and blossom essences.', 'assets/images/perfumes/Miss-Dior-Blooming-Bouquet.jpg', 'bi-flower1', '2026-10-27 19:57:01'),
(12, 'Woody', 'Perfume', 'Warm, deep, and earthy fragrances enriched with sandalwood, cedar, vetiver, and rich woods.', 'assets/images/perfumes/BLEU-DE-CHANEL-Parfum.jpg', 'bi-tree', '2026-10-27 19:57:01'),
(13, 'Fresh', 'Perfume', 'Crisp, invigorating, and clean scents with zesty citrus, aquatic breezes, and green notes.', 'assets/images/perfumes/Dior-Sauvage-Eau-de-Parfum.jpg', 'bi-droplet', '2026-10-27 19:57:01'),
(14, 'Oriental', 'Perfume', 'Sensual, exotic, and opulent compositions rich in warm amber, spices, and vanilla.', 'assets/images/perfumes/Versace-Crystal-Noir-Eau-de-Parfum.jpg', 'bi-sun', '2026-10-27 19:57:02'),
(15, 'Fruity', 'Perfume', 'Vibrant, juicy, and sweet olfactive profiles bursting with berries, peach, apple, and exotic fruits.', 'assets/images/perfumes/CREED-AVENTUS-Eau-de-Parfum.jpg', 'bi-apple', '2026-10-27 19:57:02');
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `profiles`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `profiles`
LOCK TABLES `profiles` WRITE;
INSERT INTO `profiles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Floral', 'Delicate, romantic, and blooming floral bouquets featuring rose, jasmine, and blossom essences.', '2026-10-27 19:57:01'),
(2, 'Woody', 'Warm, deep, and earthy fragrances enriched with sandalwood, cedar, vetiver, and rich woods.', '2026-10-27 19:57:01'),
(3, 'Fresh', 'Crisp, invigorating, and clean scents with zesty citrus, aquatic breezes, and green notes.', '2026-10-27 19:57:01'),
(4, 'Oriental', 'Sensual, exotic, and opulent compositions rich in warm amber, spices, and vanilla.', '2026-10-27 19:57:01'),
(5, 'Fruity', 'Vibrant, juicy, and sweet olfactive profiles bursting with berries, peach, apple, and exotic fruits.', '2026-10-27 19:57:01');
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `contacts`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `contacts`
LOCK TABLES `contacts` WRITE;
INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Test Customer', 'test@example.com', 'Inquiry about Rolex Submariner', 'Hello, I would like to inquire about the availability of the Rolex Submariner Date model.', '2026-10-21 20:49:02'),
(2, 'dhruv ramoliya', 'dhruv5674@gmail.com', 'product inquiry', 'Hello, I am interested in your product. please provide more information.\r\nThank You.', '2026-10-21 20:54:19');
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Watch',
  `profile` varchar(50) DEFAULT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `products`
LOCK TABLES `products` WRITE;
INSERT INTO `products` (`id`, `name`, `brand`, `category`, `profile`, `description`, `price`, `discount_price`, `stock`, `image`, `rating`, `created_at`) VALUES
(1, 'Hublot Big Bang Watch', 'Hublot', 'Sports', NULL, 'Bold and striking design. Perfect for Men. Sports watch.', 1200000.00, NULL, 10, 'assets/images/watches/Hublot_Big_Bang.jpg', 0.0, '2026-08-16 16:04:34'),
(2, 'Ballon Bleu de Cartier Watch', 'Cartier', 'Classic', NULL, 'Elegant and sophisticated. A true masterpiece. Perfect for Women. Classic watch.', 580000.00, NULL, 10, 'assets/images/watches/cartier-ballon-bleu-de-cartier.jpg', 0.0, '2026-08-16 16:04:34'),
(3, 'Cartier Baignoire Watch', 'Cartier', 'Dress', NULL, 'Graceful and timeless design with exquisite oval contours. Perfect for Women. Dress watch.', 1120000.00, NULL, 10, 'assets/images/watches/cartier-naignoire.jpg', 0.0, '2026-08-16 16:04:34'),
(4, 'Cartier Tank Watch', 'Cartier', 'Dress', NULL, 'An iconic rectangular luxury timepiece for black-tie elegance. A perfect Unisex watch. Dress watch.', 320000.00, NULL, 10, 'assets/images/watches/cartier-tank.jpg', 0.0, '2026-08-16 16:04:34'),
(5, 'Hublot Classic Fusion Watch', 'Hublot', 'Classic', NULL, 'A perfect blend of tradition and innovation. Perfect for Men. Classic watch.', 740000.00, NULL, 9, 'assets/images/watches/hublot_classic_fusion.jpg', 0.0, '2026-08-16 16:04:34'),
(6, 'Omega Constellation Watch', 'Omega', 'Classic', NULL, 'Luxurious and refined with iconic claws. Perfect for Women. Classic watch.', 620000.00, NULL, 10, 'assets/images/watches/omega-constallation.jpg', 0.0, '2026-08-16 16:04:34'),
(7, 'Omega Seamaster Watch', 'Omega', 'Diver', NULL, 'The ultimate diving watch with unparalleled precision. Perfect for Men. Diver watch.', 560000.00, NULL, 10, 'assets/images/watches/omega-sea-master.jpg', 0.0, '2026-08-16 16:04:34'),
(8, 'Omega De Ville Prestige Watch', 'Omega', 'Dress', NULL, 'Classic styling with luxury finishes tailored for formal attire. A perfect Unisex watch. Dress watch.', 410000.00, NULL, 10, 'assets/images/watches/omega-ville-prestige.jpg', 0.0, '2026-08-16 16:04:34'),
(9, 'Rolex Day-Date Watch', 'Rolex', 'Classic', NULL, 'The ultimate prestige watch. Perfect for Men. Classic watch.', 3450000.00, NULL, 10, 'assets/images/watches/rolex-day-date.jpg', 0.0, '2026-08-16 16:04:34'),
(10, 'Rolex Lady-Datejust Watch', 'Rolex', 'Classic', NULL, 'The classic Rolex feminine watch. Perfect for Women. Classic watch.', 980000.00, NULL, 10, 'assets/images/watches/rolex-lady-datejust.jpg', 0.0, '2026-08-16 16:04:34'),
(11, 'Rolex Oyster Perpetual Watch', 'Rolex', 'Diver', NULL, 'The essence of the waterproof Oyster case engineered for deep sea and oceanic exploration. A perfect Unisex watch. Diver watch.', 680000.00, NULL, 10, 'assets/images/watches/rolex_oyster_perpetual.jpg', 0.0, '2026-08-16 16:04:34'),
(12, 'Gucci 1500L Rectangular Watch', 'Gucci', 'Dress', NULL, 'Elegant and slim rectangular bangle design. Perfect for Women. Dress watch.', 95000.00, NULL, 10, 'assets/images/watches/Gucci-1500L-rectangular-watch.jpg', 0.0, '2026-08-16 16:43:09'),
(14, 'Gucci Interlocking Chronograph Watch', 'Gucci', 'Sports', NULL, 'A bold statement piece with interlocking G design and chronograph precision. Perfect for Men. Sports watch.', 185000.00, NULL, 7, 'assets/images/watches/Gucci-Interlocking-Chronograph-YA142316.jpg', 0.0, '2026-08-16 16:51:47'),
(15, 'Bleu de Chanel Perfume', 'Chanel', 'Perfume', 'Woody', 'A powerful and refined fragrance. Perfect for Men.', 15500.00, NULL, 10, 'assets/images/perfumes/BLEU-DE-CHANEL-Parfum.jpg', 0.0, '2026-08-16 17:52:58'),
(16, 'Chance Eau Tendre Perfume', 'Chanel', 'Perfume', 'Fruity', 'A delicate and radiant floral-fruity fragrance. Perfect for Women.', 14800.00, NULL, 10, 'assets/images/perfumes/Chance-Eau-Tendre-Eau-de-Parfum.jpg', 0.0, '2026-08-16 17:52:58'),
(17, 'Chanel Beige Eau de Perfume', 'Chanel', 'Perfume', 'Floral', 'A voluptuous floral fragrance. A perfect Unisex scent.', 32500.00, NULL, 10, 'assets/images/perfumes/CHANEL-BEIGE-Eau-de-Parfum.jpg', 0.0, '2026-08-16 17:52:58'),
(18, 'Creed Aventus Perfume', 'Creed', 'Perfume', 'Fruity', 'The legendary masculine fragrance. Perfect for Men.', 36500.00, NULL, 10, 'assets/images/perfumes/CREED-AVENTUS-Eau-de-Parfum.jpg', 0.0, '2026-08-16 17:52:58'),
(19, 'Creed Aventus For Her Perfume', 'Creed', 'Perfume', 'Fruity', 'The feminine counterpart to the iconic Aventus. Perfect for Women.', 33000.00, NULL, 8, 'assets/images/perfumes/CREED-Aventus-For-Her.jpg', 0.0, '2026-08-16 17:52:58'),
(20, 'Creed Silver Mountain Water Perfume', 'Creed', 'Perfume', 'Fresh', 'A refreshing, contemporary scent. A perfect Unisex fragrance.', 31500.00, NULL, 10, 'assets/images/perfumes/CREED-Silver-Mountain-Water.jpg', 0.0, '2026-08-16 17:52:58'),
(21, 'Dior Homme Eau de Toilette Perfume', 'Dior', 'Perfume', 'Woody', 'A woody, spicy signature scent. Perfect for Men.', 11500.00, NULL, 10, 'assets/images/perfumes/Dior-Homme-Eau-de-Toilette.jpg', 0.0, '2026-08-16 17:52:58'),
(22, 'Miss Dior Blooming Bouquet Perfume', 'Dior', 'Perfume', 'Floral', 'A sparkling floral composition. Perfect for Women.', 13200.00, NULL, 9, 'assets/images/perfumes/Miss-Dior-Blooming-Bouquet.jpg', 0.0, '2026-08-16 17:52:58'),
(23, 'Dior Sauvage Perfume', 'Dior', 'Perfume', 'Fresh', 'A radically fresh and raw scent. A perfect Unisex choice.', 14500.00, NULL, 10, 'assets/images/perfumes/Dior-Sauvage-Eau-de-Parfum.jpg', 0.0, '2026-08-16 17:52:58'),
(24, 'Versace Eros Perfume', 'Versace', 'Perfume', 'Fresh', 'Passionate and strong. Perfect for Men.', 8800.00, NULL, 10, 'assets/images/perfumes/Versace-Eros-Eau-de-Toilette.jpg', 0.0, '2026-08-16 17:52:58'),
(25, 'Versace Bright Crystal Perfume', 'Versace', 'Perfume', 'Floral', 'A vibrant, fresh floral fragrance. Perfect for Women.', 8500.00, NULL, 10, 'assets/images/perfumes/Versace-Bright-Crystal-Eau-de-Toilette.jpg', 0.0, '2026-08-16 17:52:58'),
(26, 'Versace Crystal Noir Perfume', 'Versace', 'Perfume', 'Oriental', 'A sensual and delicate fragrance. A perfect Unisex scent.', 9400.00, NULL, 10, 'assets/images/perfumes/Versace-Crystal-Noir-Eau-de-Parfum.jpg', 0.0, '2026-08-16 17:52:58'),
(27, 'Y Le Perfume', 'Yves Saint Laurent', 'Perfume', 'Woody', 'A bold and woody fragrance. Perfect for Men.', 13800.00, NULL, 8, 'assets/images/perfumes/YSL-Y-Le-Parfum.jpg', 0.0, '2026-08-16 17:52:59'),
(28, 'Mon Paris Eau de Perfume', 'Yves Saint Laurent', 'Perfume', 'Fruity', 'A passionate, romantic scent. Perfect for Women.', 12600.00, NULL, 10, 'assets/images/perfumes/Mon-Paris-Eau-de-Parfum.jpg', 5.0, '2026-08-16 17:52:59'),
(29, 'YSL Libre Intense Perfume', 'Yves Saint Laurent', 'Perfume', 'Oriental', 'A bold and fiery floral scent. A perfect Unisex fragrance.', 14200.00, NULL, 9, 'assets/images/perfumes/Yves-Saint-Laurent-Y-Le-Parfum.jpg', 0.0, '2026-08-16 17:52:59'),
(91, 'Gucci G-Timeless Watch', 'Gucci', 'Classic', NULL, 'A versatile and iconic timepiece. A perfect Unisex watch. Classic watch.', 145000.00, NULL, 10, 'assets/images/watches/Gucci-G-timeless.jpg', 0.0, '2026-08-16 16:43:09'),
(122, 'Hublot Classic Luxury Watch', 'Hublot', 'Classic', NULL, 'A luxurious unisex watch by Hublot. Classic watch.', 890000.00, NULL, 0, 'assets/images/featured products/Hublot geneve.jpg', 0.0, '2026-09-28 19:49:01'),
(123, 'Pour Monsieur', 'Chanel', 'Perfume', 'Woody', 'Experience the exquisite essence of Pour Monsieur. Designed for those who appreciate luxury, elegance, and unforgettable signature scents.', 10200.00, NULL, 10, 'assets/images/perfumes/pour-monsieur.jpg', 0.0, '2026-10-22 13:49:55');
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `reviews`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `orders`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('Pending','Confirmed','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `order_items`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `site_settings`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_otp` varchar(6) DEFAULT NULL,
  `reset_otp_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `wishlist`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS=1;
