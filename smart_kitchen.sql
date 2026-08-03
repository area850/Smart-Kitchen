-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2025 at 06:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart_kitchen`
--

-- --------------------------------------------------------

--
-- Table structure for table `finance_orders`
--

CREATE TABLE `finance_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `items` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','ready','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vat` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cooking_time` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finance_orders`
--

INSERT INTO `finance_orders` (`id`, `order_id`, `items`, `total_price`, `status`, `created_at`, `vat`, `service_tax`, `total`, `cooking_time`) VALUES
(442, 200, 'black-forest (x1)', 170.00, 'completed', '2025-08-12 20:17:12', 25.50, 8.50, 204.00, ''),
(443, 201, 'black-forest (x100) - 10 min', 17000.00, 'completed', '2025-08-15 16:33:49', 2550.00, 850.00, 20400.00, ''),
(444, 202, 'black-forest (x27) - 10 min', 4590.00, 'completed', '2025-08-15 17:01:15', 688.50, 229.50, 5508.00, ''),
(445, 203, 'black-forest (x70) - 10 min', 11900.00, 'completed', '2025-08-15 17:03:20', 1785.00, 595.00, 14280.00, ''),
(446, 204, 'black-forest (x1) - 10 min', 170.00, 'completed', '2025-08-15 17:04:12', 25.50, 8.50, 204.00, ''),
(447, 205, 'black-forest (x1) - 10 min, white-forest (x1) - 10 min', 340.00, 'completed', '2025-08-15 17:06:00', 51.00, 17.00, 408.00, ''),
(448, 206, 'black-forest (x1) - 10 min', 170.00, 'completed', '2025-08-15 17:12:32', 25.50, 8.50, 204.00, ''),
(449, 207, 'white-forest (x1) - 10 min', 170.00, 'completed', '2025-08-15 18:56:05', 25.50, 8.50, 204.00, ''),
(450, 208, 'machiato (x1), latte (x1)', 300.00, 'completed', '2025-08-15 22:09:25', 45.00, 15.00, 360.00, ''),
(451, 209, 'white-forest (x4) - 10 min', 639.96, 'pending', '2025-08-15 22:19:42', 0.00, 0.00, 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `food_drink`
--

CREATE TABLE `food_drink` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `ing_1` varchar(100) NOT NULL,
  `ing_2` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `cooking_time` varchar(20) NOT NULL,
  `frozen` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_drink`
--

INSERT INTO `food_drink` (`id`, `name`, `category`, `price`, `ing_1`, `ing_2`, `image`, `cooking_time`, `frozen`, `created_at`, `updated_at`) VALUES
(2, 'kitfo', 'yefisik', 840.00, 'meat', 'kocho', 'kitfo.jpg', '10', 1, '2025-08-08 04:16:13', '2025-08-15 22:20:19'),
(7, 'tea', 'hotdrink', 35.00, 'tea plant,', 'water', 'tea2.jpg', '3', 1, '2025-08-08 04:16:13', '2025-08-15 22:20:17'),
(8, 'avocado', 'juice', 100.00, 'avocado', ',water,sugar', 'avocado.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(9, 'mango', 'juice', 100.00, 'mango', ',water,suger', 'mango.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(10, 'pinaple', 'juice', 120.00, 'pinaple,', 'water,suger', 'pineapple.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(11, 'papaye', 'juice', 120.00, 'papaye', ',water,sugar', 'papaya.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(12, 'strawbery', 'juice', 200.00, 'strawbery,', 'water,sugar', 'strawbery.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(14, 'machiato', 'hotdrink', 80.00, 'milk', ',coffee', 'machiato.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(15, 'hot chocolate', 'hotdrink', 250.00, 'milk', 'cocoa powder,sugar', 'chocolate.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(16, 'latte', 'hotdrink', 220.00, 'espresso,stemaed milk', ',thin layer of foam', 'lattee.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(17, 'espresso', 'hotdrink', 80.00, 'finley ground coffee beans,', 'hot water', 'espresso.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(19, 'special tea', 'hotdrink', 50.00, 'ginger,orange juice,lemon,', 'garlic,honey,pinaple jucie', 'spectea.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(20, 'ice coffee', 'hotdrink', 100.00, 'warm water,coffee granules,sugar,', 'ice,cold milk', 'ice coffee.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(24, 'shawarma', 'yefisik', 280.00, 'chicken,pita-bread,', 'onion,romaine', 'shawar.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(26, 'pizza', 'yefisik', 320.00, 'pizza-dough,mozzerella,', 'olives,tomatoes', 'pizzaa.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(29, 'vegetable-rice', 'yetsom', 200.00, 'vegetables,rice,', 'onion,carrot', 'veggie.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(31, 'black-forest', 'dessert', 160.00, 'cocoa-powder', 'butter-milk,eggs', 'black-forest.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(32, 'white-forest', 'dessert', 159.99, 'butter,flour', 'eggs,corn-starch', 'white-forest.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24'),
(84, 'Borrito', 'yetsom', 120.00, 'vegitable', 'carrot', 'img_689435cf7ba775.58906593.jpg', '10', 0, '2025-08-08 04:16:13', '2025-08-15 21:17:24');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `qty` varchar(50) NOT NULL,
  `expiry` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `qty`, `expiry`) VALUES
(2, 'cds', 'dcs', '2025-08-09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `items` text NOT NULL,
  `status` enum('pending','preparing','ready') DEFAULT 'pending',
  `ing_1` varchar(120) NOT NULL,
  `ing_2` varchar(120) NOT NULL,
  `price` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cooking_time` varchar(20) NOT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `items`, `status`, `ing_1`, `ing_2`, `price`, `created_at`, `cooking_time`, `completed_at`) VALUES
(200, 'black-forest', '', 'cocoa-powder', 'butter-milk,eggs', '', '2025-08-12 20:17:12', '', '2025-08-12 20:17:22'),
(201, 'black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest', '', 'cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-po', 'butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,', '', '2025-08-15 16:33:49', '1000', NULL),
(202, 'black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest', '', 'cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-po', 'butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,', '', '2025-08-15 17:01:15', '270', NULL),
(203, 'black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest', '', 'cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-po', 'butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,', '', '2025-08-15 17:03:20', '700', NULL),
(204, 'black-forest', '', 'cocoa-powder', 'butter-milk,eggs', '', '2025-08-15 17:04:12', '10', NULL),
(205, 'black-forest, white-forest', '', 'cocoa-powder, butter,flour', 'butter-milk,eggs, eggs,corn-starch', '', '2025-08-15 17:06:00', '20', NULL),
(206, 'black-forest', '', 'cocoa-powder', 'butter-milk,eggs', '', '2025-08-15 17:12:32', '10', NULL),
(207, 'white-forest', '', 'butter,flour', 'eggs,corn-starch', '', '2025-08-15 18:56:05', '10', NULL),
(208, 'machiato, latte', '', 'milk, espresso,stemaed milk', ',coffee, ,thin layer of foam', '', '2025-08-15 22:09:25', '20', NULL),
(209, 'white-forest, white-forest, white-forest, white-forest', 'ready', 'butter,flour, butter,flour, butter,flour, butter,flour', 'eggs,corn-starch, eggs,corn-starch, eggs,corn-starch, eggs,corn-starch', '', '2025-08-15 22:19:42', '40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_image`, `name`, `remember_token`, `token_expiry`, `created_at`, `updated_at`) VALUES
(1, 'admin', '', 'admin123', NULL, 'admin', NULL, NULL, '2025-08-13 19:19:19', '2025-08-15 22:57:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `finance_orders`
--
ALTER TABLE `finance_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `food_drink`
--
ALTER TABLE `food_drink`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `email_idx` (`email`),
  ADD KEY `username_idx` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `finance_orders`
--
ALTER TABLE `finance_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=452;

--
-- AUTO_INCREMENT for table `food_drink`
--
ALTER TABLE `food_drink`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
