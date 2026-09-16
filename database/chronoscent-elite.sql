-- phpMyAdmin SQL Dump
-- Database: `chronoscent-elite`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- --------------------------------------------------------

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `category` enum('Watch','Perfume') NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`name`, `brand`, `category`, `description`, `price`, `discount_price`, `stock`, `image`, `rating`) VALUES
('Submariner Date', 'Rolex', 'Watch', 'The reference among divers watches.', 10500.00, 9999.00, 5, 'assets/images/watches/1.jpg', 4.9),
('Seamaster Diver 300M', 'Omega', 'Watch', 'A legendary dive watch since 1993.', 5200.00, 4800.00, 8, 'assets/images/watches/2.jpg', 4.8),
('Nautilus', 'Patek Philippe', 'Watch', 'Elegant sports watch.', 35000.00, NULL, 2, 'assets/images/watches/3.jpg', 5.0),
('Royal Oak', 'Audemars Piguet', 'Watch', 'The iconic luxury sports watch.', 28000.00, NULL, 3, 'assets/images/watches/4.jpg', 4.9),
('Carrera Calibre Heuer 02', 'TAG Heuer', 'Watch', 'Racing inspired chronograph.', 5800.00, 5400.00, 10, 'assets/images/watches/5.jpg', 4.5),
('Navitimer', 'Breitling', 'Watch', 'Aviation chronograph.', 8200.00, 7800.00, 6, 'assets/images/watches/6.jpg', 4.7),
('Luminor Marina', 'Panerai', 'Watch', 'Distinctive Italian design.', 7500.00, 7000.00, 7, 'assets/images/watches/7.jpg', 4.6),
('Big Bang', 'Hublot', 'Watch', 'The art of fusion.', 15000.00, 14000.00, 4, 'assets/images/watches/8.jpg', 4.4),
('Oyster Perpetual 41', 'Rolex', 'Watch', 'Classic and timeless.', 6100.00, NULL, 12, 'assets/images/watches/9.jpg', 4.8),
('Speedmaster Moonwatch', 'Omega', 'Watch', 'The first watch on the moon.', 6500.00, 6200.00, 9, 'assets/images/watches/10.jpg', 4.9),

('Bleu de Chanel', 'Chanel', 'Perfume', 'A woody aromatic fragrance for men.', 150.00, 135.00, 30, 'assets/images/perfumes/1.jpg', 4.8),
('Sauvage', 'Dior', 'Perfume', 'A radically fresh composition.', 145.00, 125.00, 25, 'assets/images/perfumes/2.jpg', 4.7),
('Aventus', 'Creed', 'Perfume', 'Fruity and rich fragrance.', 450.00, 420.00, 10, 'assets/images/perfumes/3.jpg', 4.9),
('Baccarat Rouge 540', 'Maison Francis Kurkdjian', 'Perfume', 'Luminous and sophisticated.', 325.00, 300.00, 15, 'assets/images/perfumes/4.jpg', 5.0),
('Oud Wood', 'Tom Ford', 'Perfume', 'Rare, exotic, distinctive.', 275.00, 250.00, 20, 'assets/images/perfumes/5.jpg', 4.8),
('Le Male', 'Jean Paul Gaultier', 'Perfume', 'The sailor. Intense and sensible.', 105.00, 95.00, 40, 'assets/images/perfumes/6.jpg', 4.5),
('Black Orchid', 'Tom Ford', 'Perfume', 'Luxurious and sensual.', 150.00, 135.00, 22, 'assets/images/perfumes/7.jpg', 4.6),
('Terre d\'Hermes', 'Hermes', 'Perfume', 'Woody, vegetal, mineral.', 135.00, 120.00, 28, 'assets/images/perfumes/8.jpg', 4.7),
('Y Eau de Parfum', 'Yves Saint Laurent', 'Perfume', 'Deep, fresh and masculine.', 140.00, 125.00, 35, 'assets/images/perfumes/9.jpg', 4.6),
('Acqua Di Giò Profumo', 'Giorgio Armani', 'Perfume', 'Deep and aquatic.', 160.00, 145.00, 18, 'assets/images/perfumes/10.jpg', 4.8);

-- --------------------------------------------------------

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

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
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `valid_until` date NOT NULL,
  `is_active` boolean DEFAULT true,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `coupons` (`code`, `discount_percent`, `valid_until`) VALUES
('WELCOME10', 10.00, '2026-12-31');

COMMIT;
