-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2024 at 03:38 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laundry_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `code` char(6) NOT NULL,
  `discount` decimal(5,2) NOT NULL,
  `startAt` bigint(20) UNSIGNED NOT NULL,
  `endAt` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL COMMENT '0 = Tidak Aktif; 1 = Aktif',
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` char(36) NOT NULL,
  `couponId` char(36) DEFAULT NULL,
  `code` char(6) NOT NULL,
  `date` bigint(20) UNSIGNED NOT NULL,
  `amountPaid` decimal(10,2) UNSIGNED DEFAULT NULL,
  `paidAt` bigint(20) UNSIGNED DEFAULT NULL,
  `workEndAt` bigint(20) UNSIGNED DEFAULT NULL,
  `endAt` bigint(20) UNSIGNED DEFAULT NULL,
  `customerName` varchar(255) NOT NULL,
  `customerPhone` varchar(255) NOT NULL,
  `customerEmail` varchar(255) DEFAULT NULL,
  `customerAddress` text DEFAULT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

CREATE TABLE `order_products` (
  `id` char(36) NOT NULL,
  `orderId` char(36) NOT NULL,
  `productId` char(36) NOT NULL,
  `quantity` bigint(20) UNSIGNED NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) UNSIGNED NOT NULL COMMENT 'batas banyak barang untuk harga yang ditentukan',
  `maximumQuantity` decimal(10,2) NOT NULL COMMENT 'jumlah maksimal yang bisa dibeli',
  `unit` varchar(255) NOT NULL COMMENT 'satuan barang',
  `time` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL COMMENT '0 = Tidak Aktif; 1 = Aktif',
  `smallImage` varchar(255) DEFAULT NULL,
  `mediumImage` varchar(255) DEFAULT NULL,
  `largeImage` varchar(255) DEFAULT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_items`
--

CREATE TABLE `product_items` (
  `id` char(36) NOT NULL,
  `itemId` char(36) NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_services`
--

CREATE TABLE `product_services` (
  `id` char(36) NOT NULL,
  `productId` char(36) NOT NULL,
  `serviceId` char(36) NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` char(36) NOT NULL,
  `orderCode` char(6) NOT NULL,
  `rate` smallint(10) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `createdAt` bigint(20) UNSIGNED NOT NULL,
  `updatedAt` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `description` (`description`(768));

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `customerName` (`customerName`),
  ADD KEY `customerPhone` (`customerPhone`),
  ADD KEY `customerEmail` (`customerEmail`),
  ADD KEY `couponId` (`couponId`),
  ADD KEY `date` (`date`),
  ADD KEY `customerAddress` (`customerAddress`(768)),
  ADD KEY `endAt` (`endAt`),
  ADD KEY `workEndAt` (`workEndAt`),
  ADD KEY `paidAt` (`paidAt`),
  ADD KEY `amountPaid` (`amountPaid`),
  ADD KEY `amountPaid_2` (`amountPaid`);

--
-- Indexes for table `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orderId` (`orderId`,`productId`),
  ADD KEY `productId` (`productId`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_items`
--
ALTER TABLE `product_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itemId` (`itemId`);

--
-- Indexes for table `product_services`
--
ALTER TABLE `product_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productId` (`productId`),
  ADD KEY `serviceId` (`serviceId`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderCode` (`orderCode`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `status` (`description`(768)),
  ADD KEY `description` (`description`(768));

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `name` (`name`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`couponId`) REFERENCES `coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_products`
--
ALTER TABLE `order_products`
  ADD CONSTRAINT `order_products_ibfk_1` FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_products_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_items`
--
ALTER TABLE `product_items`
  ADD CONSTRAINT `product_items_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_services`
--
ALTER TABLE `product_services`
  ADD CONSTRAINT `product_services_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_services_ibfk_2` FOREIGN KEY (`serviceId`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`orderCode`) REFERENCES `orders` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
