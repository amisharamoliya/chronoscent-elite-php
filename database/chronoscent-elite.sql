-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: chronoscent-elite
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','$2y$10$OEFAcPclg8PdWucs6QCJSevMLtTE9YmSxonRBFZBHonLhszG4H.ba');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Rolex','Watch','Geneva, Switzerland','Pioneering Swiss luxury horology and timeless precision timepieces.','assets/images/brands/rolex.jpg','2026-10-21 10:10:54'),(2,'Omega','Watch','Biel, Switzerland','Master chronometers and legendary official Olympic & moon landing horology.','assets/images/brands/omega.jpg','2026-10-21 10:10:54'),(3,'Cartier','Watch','Paris, France','The jeweller of kings, iconic shaped watch cases and high Parisian elegance.','assets/images/brands/cartier.jpg','2026-10-21 10:10:54'),(4,'Gucci','Watch','Florence, Italy','Italian contemporary craftsmanship with Swiss high-precision movements.','assets/images/brands/gucci.jpg','2026-10-21 10:10:54'),(5,'Hublot','Watch','Nyon, Switzerland','Pioneering the Art of Fusion with innovative materials and bold designs.','assets/images/brands/hublot.jpg','2026-10-21 10:10:54'),(6,'Chanel','Perfume','Paris, France','Timeless French luxury haute parfumerie with iconic olfactive masterpieces.','assets/images/brands/chanel.jpg','2026-10-21 10:10:54'),(7,'Dior','Perfume','Paris, France','Radically fresh, sophisticated compositions of French couture perfumery.','assets/images/brands/dior.jpg','2026-10-21 10:10:54'),(8,'Creed','Perfume','Paris, France','Seven generations of royal heritage hand-blended artisanal essences.','assets/images/brands/creed.jpg','2026-10-21 10:10:54'),(9,'Versace','Perfume','Milan, Italy','Bold, glamorous, and sensual fragrances celebrating Italian luxury.','assets/images/brands/versace.jpg','2026-10-21 10:10:55'),(10,'Yves Saint Laurent','Perfume','Paris, France','Parisian couture perfumes blending rebellious passion with timeless elegance.','assets/images/brands/ysl.jpg','2026-10-21 10:10:55');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Watch','Watch','Precision Swiss and European automatic, mechanical, and chronograph timepieces from heritage watchmakers.','assets/images/cat_watch.png','bi-clock-history','2026-10-22 09:06:11'),(2,'Perfume','Perfume','Artisanal Eau de Parfum, Extrait de Parfum, and couture essences crafted by revered Parisian and European fragrance houses.','assets/images/cat_perfume.png','bi-flower1','2026-10-22 09:06:11'),(3,'Classic','Watch','Timeless, elegant, and sophisticated heritage designs crafted with traditional precision.','assets/images/watches/cartier-ballon-bleu-de-cartier.jpg','bi-award','2026-10-22 09:06:11'),(4,'Sports','Watch','High-performance chronographs, professional diving timepieces, and durable luxury sports watches.','assets/images/watches/Hublot_Big_Bang.jpg','bi-speedometer2','2026-10-22 09:06:11'),(5,'Modern','Perfume','Contemporary, fresh, and avant-garde luxury fragrance formulations for modern connoisseurs.','assets/images/perfumes/YSL-Y-Le-Parfum.jpg','bi-gem','2026-10-22 09:06:11'),(6,'Signature','Perfume','Rare, artisanal olfactive creations and exclusive haute parfumerie defining unforgettable prestige.','assets/images/perfumes/CREED-AVENTUS-Eau-de-Parfum.jpg','bi-stars','2026-10-22 09:06:11');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'Test Customer','test@example.com','Inquiry about Rolex Submariner','Hello, I would like to inquire about the availability of the Rolex Submariner Date model.','2026-10-21 15:19:02'),(2,'dhruv ramoliya','dhruv5674@gmail.com','product inquiry','Hello, I am interested in your product. please provide more information.\r\nThank You.','2026-10-21 15:24:19');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `valid_until` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'WELCOME10',10.00,'2026-12-31',1),(2,'NICE20',30.00,'2026-12-31',1);
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (28,28,3,1,1200.00),(29,29,14,1,1750.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (2,1,'Ramoliya Amishaben Nareshbhai','amisharamoliya2007@gmail.com','08849959097','410, pramukh park society ,punagam, Surat City, Surat Gujarat India, 395010','Surat','Gujarat','395010',7000.00,0.00,'Cash on Delivery','Shipped','2026-08-11 14:00:59'),(28,2,'rajvi maheta','rajvi@gmail.com','07654328987','B21, pramukh park society ,punagam, Surat City, Surat Gujarat India, 395010','Surat','Gujarat','395010',1200.00,0.00,'Cash on Delivery','Pending','2026-10-21 03:59:25'),(29,1,'dhruv ramoliya','dhruv5674@gmail.com','08908765647','B21, pramukh park society ,punagam, Surat City, Surat Gujarat India, 395010','Surat','Gujarat','395010',1750.00,0.00,'Cash on Delivery','Shipped','2026-10-21 08:21:21');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Watch',
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Hublot Big Bang Watch','Hublot','Watch','Bold and striking design. Perfect for Men. Sports watch.',1280000.00,NULL,10,'assets/images/watches/Hublot_Big_Bang.jpg',0.0,'2026-08-16 10:34:34'),(2,'Ballon Bleu de Cartier Watch','Cartier','Watch','Elegant and sophisticated. A true masterpiece. Perfect for Women. Classic watch.',580000.00,NULL,10,'assets/images/watches/cartier-ballon-bleu-de-cartier.jpg',0.0,'2026-08-16 10:34:34'),(3,'Cartier Baignoire Watch','Cartier','Watch','Graceful and timeless design. Perfect for Women. Classic watch.',1120000.00,NULL,10,'assets/images/watches/cartier-naignoire.jpg',0.0,'2026-08-16 10:34:34'),(4,'Cartier Tank Watch','Cartier','Watch','An iconic rectangular luxury timepiece. A perfect Unisex watch. Classic watch.',320000.00,NULL,10,'assets/images/watches/cartier-tank.jpg',0.0,'2026-08-16 10:34:34'),(5,'Hublot Classic Fusion Watch','Hublot','Watch','A perfect blend of tradition and innovation. Perfect for Men. Classic watch.',740000.00,NULL,10,'assets/images/watches/hublot_classic_fusion.jpg',0.0,'2026-08-16 10:34:34'),(6,'Omega Constellation Watch','Omega','Watch','Luxurious and refined with iconic claws. Perfect for Women. Classic watch.',620000.00,NULL,10,'assets/images/watches/omega-constallation.jpg',0.0,'2026-08-16 10:34:34'),(7,'Omega Seamaster Watch','Omega','Watch','The ultimate diving watch with unparalleled precision. Perfect for Men. Sports watch.',560000.00,NULL,10,'assets/images/watches/omega-sea-master.jpg',0.0,'2026-08-16 10:34:34'),(8,'Omega De Ville Prestige Watch','Omega','Watch','Classic styling with luxury finishes. A perfect Unisex watch. Classic watch.',410000.00,NULL,10,'assets/images/watches/omega-ville-prestige.jpg',0.0,'2026-08-16 10:34:34'),(9,'Rolex Day-Date Watch','Rolex','Watch','The ultimate prestige watch. Perfect for Men. Classic watch.',3450000.00,NULL,10,'assets/images/watches/rolex-day-date.jpg',0.0,'2026-08-16 10:34:34'),(10,'Rolex Lady-Datejust Watch','Rolex','Watch','The classic Rolex feminine watch. Perfect for Women. Classic watch.',980000.00,NULL,10,'assets/images/watches/rolex-lady-datejust.jpg',0.0,'2026-08-16 10:34:34'),(11,'Rolex Oyster Perpetual Watch','Rolex','Watch','The essence of the Oyster. A perfect Unisex watch. Sports watch.',680000.00,NULL,10,'assets/images/watches/rolex_oyster_perpetual.jpg',0.0,'2026-08-16 10:34:34'),(12,'Gucci 1500L Rectangular Watch','Gucci','Watch','Elegant and slim rectangular design. Perfect for Women. Classic watch.',95000.00,NULL,10,'assets/images/watches/Gucci-1500L-rectangular-watch.jpg',0.0,'2026-08-16 11:13:09'),(14,'Gucci Interlocking Chronograph Watch','Gucci','Watch','A bold statement piece with interlocking G design. Perfect for Men. Sports watch.',185000.00,NULL,9,'assets/images/watches/Gucci-Interlocking-Chronograph-YA142316.jpg',0.0,'2026-08-16 11:21:47'),(15,'Bleu de Chanel Perfume','Chanel','Perfume','A powerful and refined fragrance. Perfect for Men.',15500.00,NULL,10,'assets/images/perfumes/BLEU-DE-CHANEL-Parfum.jpg',0.0,'2026-08-16 12:22:58'),(16,'Chance Eau Tendre Perfume','Chanel','Perfume','A delicate and radiant floral-fruity fragrance. Perfect for Women.',14800.00,NULL,10,'assets/images/perfumes/Chance-Eau-Tendre-Eau-de-Parfum.jpg',0.0,'2026-08-16 12:22:58'),(17,'Chanel Beige Eau de Perfume','Chanel','Perfume','A voluptuous floral fragrance. A perfect Unisex scent.',32500.00,NULL,10,'assets/images/perfumes/CHANEL-BEIGE-Eau-de-Parfum.jpg',0.0,'2026-08-16 12:22:58'),(18,'Creed Aventus Perfume','Creed','Perfume','The legendary masculine fragrance. Perfect for Men.',36500.00,NULL,10,'assets/images/perfumes/CREED-AVENTUS-Eau-de-Parfum.jpg',0.0,'2026-08-16 12:22:58'),(19,'Creed Aventus For Her Perfume','Creed','Perfume','The feminine counterpart to the iconic Aventus. Perfect for Women.',33000.00,NULL,8,'assets/images/perfumes/CREED-Aventus-For-Her.jpg',0.0,'2026-08-16 12:22:58'),(20,'Creed Silver Mountain Water Perfume','Creed','Perfume','A refreshing, contemporary scent. A perfect Unisex fragrance.',31500.00,NULL,10,'assets/images/perfumes/CREED-Silver-Mountain-Water.jpg',0.0,'2026-08-16 12:22:58'),(21,'Dior Homme Eau de Toilette Perfume','Dior','Perfume','A woody, spicy signature scent. Perfect for Men.',11500.00,NULL,10,'assets/images/perfumes/Dior-Homme-Eau-de-Toilette.jpg',0.0,'2026-08-16 12:22:58'),(22,'Miss Dior Blooming Bouquet Perfume','Dior','Perfume','A sparkling floral composition. Perfect for Women.',13200.00,NULL,9,'assets/images/perfumes/Miss-Dior-Blooming-Bouquet.jpg',0.0,'2026-08-16 12:22:58'),(23,'Dior Sauvage Perfume','Dior','Perfume','A radically fresh and raw scent. A perfect Unisex choice.',14500.00,NULL,10,'assets/images/perfumes/Dior-Sauvage-Eau-de-Parfum.jpg',0.0,'2026-08-16 12:22:58'),(24,'Versace Eros Perfume','Versace','Perfume','Passionate and strong. Perfect for Men.',8800.00,NULL,10,'assets/images/perfumes/Versace-Eros-Eau-de-Toilette.jpg',0.0,'2026-08-16 12:22:58'),(25,'Versace Bright Crystal Perfume','Versace','Perfume','A vibrant, fresh floral fragrance. Perfect for Women.',8500.00,NULL,10,'assets/images/perfumes/Versace-Bright-Crystal-Eau-de-Toilette.jpg',0.0,'2026-08-16 12:22:58'),(26,'Versace Crystal Noir Perfume','Versace','Perfume','A sensual and delicate fragrance. A perfect Unisex scent.',9400.00,NULL,10,'assets/images/perfumes/Versace-Crystal-Noir-Eau-de-Parfum.jpg',0.0,'2026-08-16 12:22:58'),(27,'Y Le Perfume','Yves Saint Laurent','Perfume','A bold and woody fragrance. Perfect for Men.',13800.00,NULL,10,'assets/images/perfumes/YSL-Y-Le-Parfum.jpg',0.0,'2026-08-16 12:22:59'),(28,'Mon Paris Eau de Perfume','Yves Saint Laurent','Perfume','A passionate, romantic scent. Perfect for Women.',12600.00,NULL,10,'assets/images/perfumes/Mon-Paris-Eau-de-Parfum.jpg',5.0,'2026-08-16 12:22:59'),(29,'YSL Libre Intense Perfume','Yves Saint Laurent','Perfume','A bold and fiery floral scent. A perfect Unisex fragrance.',14200.00,NULL,10,'assets/images/perfumes/Yves-Saint-Laurent-Y-Le-Parfum.jpg',0.0,'2026-08-16 12:22:59'),(91,'Gucci G-Timeless Watch','Gucci','Watch','A versatile and iconic timepiece. A perfect Unisex watch. Classic watch.',145000.00,NULL,10,'assets/images/watches/Gucci-G-timeless.jpg',0.0,'2026-08-16 11:13:09'),(122,'Hublot Classic Luxury Watch','Hublot','Watch','A luxurious unisex watch by Hublot.',890000.00,NULL,0,'assets/images/featured products/Hublot geneve.jpg',0.0,'2026-09-28 14:19:01'),(123,'Pour Monsieur','Chanel','Perfume','Experience the exquisite essence of Pour Monsieur. Designed for those who appreciate luxury, elegance, and unforgettable signature scents.',10200.00,NULL,10,'assets/images/perfumes/pour-monsieur.jpg',0.0,'2026-10-22 08:19:55');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (3,1,28,5,'Amazing product !','The product quality is very good and i really linked the design','2026-09-27 13:51:03');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ramoliya Amishaben Nareshbhai','amisharamoliya2007@gmail.com','08849959097','$2y$10$vPo/jwgDCqlTnrmIi1KGIuMmHQQDAJxGnd4NIKZrT4VMx2T5GXaxK','2026-08-11 13:25:45',NULL,NULL),(2,'rajvi maheta','rajvi@gmail.com','07654328987','$2y$10$xiQd1g5UN6V/8YKDbaZkcuOwRiEqzBlmw5ywjEaOyc33VS4ogXIYi','2026-10-21 03:58:39',NULL,NULL),(3,'priya kanani','priyakanani@gmail.com','08849959097','$2y$10$WRgeSvtoa/yQPR4wnKnfQu2K6HwI6RbzPwIKiLabIcomHFcwxGSOu','2026-10-21 13:50:20',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (26,1,29);
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 14:40:45
