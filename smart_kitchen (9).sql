-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 04:32 PM
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
(441, 199, 'white-forest (x1) - 10 min', 170.00, 'completed', '2025-08-13 07:51:44', 25.50, 8.50, 204.00, ''),
(442, 200, 'white-forest (x1) - 10 min', 170.00, 'completed', '2025-08-13 10:36:11', 25.50, 8.50, 204.00, ''),
(443, 201, 'white-forest (x1) - 10 min', 170.00, 'completed', '2025-08-13 10:39:20', 25.50, 8.50, 204.00, ''),
(444, 202, 'latte (x3) - 10 min, pizza (x1) - 10 min, white-forest (x1) - 10 min', 1480.00, 'completed', '2025-08-13 10:41:07', 222.00, 74.00, 1776.00, ''),
(445, 203, 'white-forest (x1) - 10 min', 200.00, 'completed', '2025-08-13 10:47:43', 30.00, 10.00, 240.00, ''),
(446, 204, 'white-forest (x3) - 10 min', 600.00, 'completed', '2025-08-14 07:21:51', 90.00, 30.00, 720.00, ''),
(447, 205, 'white-forest (x1) - 10 min', 159.99, 'completed', '2025-08-14 10:29:04', 24.00, 8.00, 191.99, ''),
(448, 206, 'ice coffee (x2) - 10 min', 200.00, 'completed', '2025-08-14 10:36:45', 30.00, 10.00, 240.00, ''),
(449, 207, 'avocado (x1) - 10 min', 100.00, 'completed', '2025-08-14 10:38:08', 15.00, 5.00, 120.00, ''),
(450, 208, 'shawarma (x1) - 10 min, pizza (x1) - 10 min', 700.00, 'completed', '2025-08-14 10:39:27', 105.00, 35.00, 840.00, ''),
(451, 209, 'Borrito (x7) - 10 min', 840.00, 'completed', '2025-08-14 10:42:48', 126.00, 42.00, 1008.00, ''),
(452, 210, 'pinaple (x1) - 10 min, ice coffee (x1) - 10 min, pizza (x1) - 10 min', 640.00, 'completed', '2025-08-14 10:53:16', 96.00, 32.00, 768.00, ''),
(453, 211, 'white-forest (x2) - 10 min', 319.98, 'completed', '2025-08-14 11:14:29', 48.00, 16.00, 383.98, ''),
(454, 212, 'white-forest (x1) - 10 min', 159.99, 'completed', '2025-08-14 11:16:51', 24.00, 8.00, 191.99, ''),
(455, 213, 'vegetable-rice (x1) - 10 min', 200.00, 'completed', '2025-08-14 14:22:52', 30.00, 10.00, 240.00, ''),
(456, 214, 'strawbery (x1) - 10 min', 200.00, 'completed', '2025-08-14 14:41:39', 30.00, 10.00, 240.00, ''),
(457, 215, 'tea (x1) - 3 min, ice coffee (x1) - 10 min', 135.00, 'completed', '2025-08-15 11:40:23', 20.25, 6.75, 162.00, ''),
(458, 216, 'black-forest (x5) - 10 min, white-forest (x5) - 10 min', 1599.95, 'completed', '2025-08-15 11:46:10', 239.99, 80.00, 1919.94, ''),
(459, 217, 'ice coffee (x26) - 10 min', 2600.00, 'completed', '2025-08-15 11:49:08', 390.00, 130.00, 3120.00, ''),
(460, 218, 'Borrito (x100) - 10 min', 12000.00, 'completed', '2025-08-15 13:28:54', 1800.00, 600.00, 14400.00, ''),
(461, 219, 'black-forest (x250) - 10 min, white-forest (x250) - 10 min', 79997.50, 'completed', '2025-08-15 13:38:56', 11999.63, 3999.88, 95997.00, ''),
(462, 220, 'kitfo (x322) - 10 min, shawarma (x331) - 10 min, pizza (x175) - 10 min', 436660.00, 'completed', '2025-08-15 13:45:17', 65499.00, 21833.00, 523992.00, ''),
(463, 221, 'pizza (x1) - 10 min', 99999999.99, 'completed', '2025-08-15 14:00:13', 15000000.00, 5000000.00, 99999999.99, ''),
(464, 222, 'white-forest (x1) - 10 min', 159.99, 'completed', '2025-08-15 14:08:11', 24.00, 8.00, 191.99, ''),
(465, 223, 'pizza (x1) - 10 min', 320.00, 'completed', '2025-08-15 14:47:15', 48.00, 16.00, 384.00, ''),
(466, 224, 'shawarma (x1) - 10 min', 280.00, 'completed', '2025-08-16 05:35:43', 42.00, 14.00, 336.00, ''),
(467, 225, 'white-forest (x1) - 10 min', 159.99, 'completed', '2025-08-16 06:05:31', 24.00, 8.00, 191.99, ''),
(468, 226, 'vegetable-rice (x1) - 10 min', 200.00, 'completed', '2025-08-17 08:27:42', 30.00, 10.00, 240.00, ''),
(469, 227, 'tea (x1) - 3 min, white-forest (x1) - 10 min', 194.99, 'completed', '2025-08-17 08:59:16', 29.25, 9.75, 233.99, ''),
(470, 228, 'shawarma (x1) - 10 min', 420.00, 'completed', '2025-08-17 09:19:53', 63.00, 21.00, 504.00, ''),
(471, 229, 'white-forest (x2) - 10 min', 319.98, 'completed', '2025-08-17 09:31:17', 48.00, 16.00, 383.98, ''),
(472, 230, 'black-forest (x1) - 10 min', 160.00, 'completed', '2025-08-17 11:13:45', 24.00, 8.00, 192.00, ''),
(473, 231, 'Borrito (x1000) - 10 min', 120000.00, 'completed', '2025-08-19 14:10:10', 18000.00, 6000.00, 144000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `food_drink`
--

CREATE TABLE `food_drink` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(120) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `ing_1` varchar(120) NOT NULL,
  `ing_2` varchar(120) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cooking_time` varchar(20) NOT NULL,
  `frozen` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_drink`
--

INSERT INTO `food_drink` (`id`, `name`, `category`, `price`, `ing_1`, `ing_2`, `image`, `created_at`, `cooking_time`, `frozen`) VALUES
(2, 'kitfo', 'yefisik', 840.00, 'meat', 'kocho', 'kitfo.jpg', '2025-08-08 07:16:13', '10', 0),
(7, 'tea', 'hotdrink', 35.00, 'tea plant,', 'water', 'tea2.jpg', '2025-08-08 07:16:13', '3', 0),
(8, 'avocado', 'juice', 100.00, 'avocado', ',water,sugar', 'avocado.jpg', '2025-08-08 07:16:13', '10', 0),
(9, 'mango', 'juice', 100.00, 'mango', ',water,suger', 'mango.jpg', '2025-08-08 07:16:13', '10', 0),
(10, 'pinaple', 'juice', 120.00, 'pinaple,', 'water,suger', 'pineapple.jpg', '2025-08-08 07:16:13', '10', 0),
(11, 'papaye', 'juice', 120.00, 'papaye', ',water,sugar', 'papaya.jpg', '2025-08-08 07:16:13', '10', 0),
(12, 'strawbery', 'juice', 200.00, 'strawbery,', 'water,sugar', 'strawbery.jpg', '2025-08-08 07:16:13', '10', 0),
(14, 'machiato', 'hotdrink', 80.00, 'milk', ',coffee', 'machiato.jpg', '2025-08-08 07:16:13', '10', 0),
(15, 'hot chocolate', 'hotdrink', 250.00, 'milk', 'cocoa powder,sugar', 'chocolate.jpg', '2025-08-08 07:16:13', '10', 0),
(16, 'latte', 'hotdrink', 220.00, 'espresso,stemaed milk', ',thin layer of foam', 'lattee.jpg', '2025-08-08 07:16:13', '10', 0),
(17, 'espresso', 'hotdrink', 80.00, 'finley ground coffee beans,', 'hot water', 'espresso.jpg', '2025-08-08 07:16:13', '10', 0),
(19, 'special tea', 'hotdrink', 50.00, 'ginger,orange juice,lemon,', 'garlic,honey,pinaple jucie', 'spectea.jpg', '2025-08-08 07:16:13', '10', 0),
(20, 'ice coffee', 'hotdrink', 100.00, 'warm water,coffee granules,sugar,', 'ice,cold milk', 'ice coffee.jpg', '2025-08-08 07:16:13', '10', 0),
(24, 'shawarma', 'yefisik', 420.00, 'chicken,pita-bread,', 'onion,romaine', 'shawar.jpg', '2025-08-08 07:16:13', '10', 0),
(26, 'pizza', 'yefisik', 320.00, 'pizza-dough,mozzerella,', 'olives,tomatoes', 'pizzaa.jpg', '2025-08-08 07:16:13', '10', 0),
(29, 'vegetable-rice', 'yetsom', 200.00, 'vegetables,rice,', 'onion,carrot', 'veggie.jpg', '2025-08-08 07:16:13', '10', 0),
(31, 'black-forest', 'dessert', 160.00, 'cocoa-powder', 'butter-milk,eggs', 'black-forest.jpg', '2025-08-08 07:16:13', '10', 0),
(32, 'white-forest', 'dessert', 159.99, 'butter,flour', 'eggs,corn-starch', 'white-forest.jpg', '2025-08-08 07:16:13', '10', 0),
(84, 'Borrito', 'yetsom', 120.00, 'vegitable', 'carrot', 'img_689435cf7ba775.58906593.jpg', '2025-08-08 07:16:13', '10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `items` text NOT NULL,
  `status` enum('pending','ready','completed') DEFAULT 'pending',
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
(199, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-13 07:51:44', '10', '2025-08-13 09:18:22'),
(200, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-13 10:36:11', '10', '2025-08-13 10:36:36'),
(201, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-13 10:39:19', '10', '2025-08-13 10:39:37'),
(202, 'latte, latte, latte, pizza, white-forest', 'completed', 'espresso,stemaed milk, espresso,stemaed milk, espresso,stemaed milk, pizza-dough,mozzerella,, butter,flour', ',thin layer of foam, ,thin layer of foam, ,thin layer of foam, olives,tomatoes, eggs,corn-starch', '', '2025-08-13 10:41:07', '10', '2025-08-13 10:42:20'),
(203, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-13 10:47:43', '10', '2025-08-13 10:47:56'),
(204, 'white-forest, white-forest, white-forest', 'completed', 'butter,flour, butter,flour, butter,flour', 'eggs,corn-starch, eggs,corn-starch, eggs,corn-starch', '', '2025-08-14 07:21:51', '10', NULL),
(205, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-14 10:29:04', '10', NULL),
(206, 'ice coffee, ice coffee', 'completed', 'warm water,coffee granules,sugar,, warm water,coffee granules,sugar,', 'ice,cold milk, ice,cold milk', '', '2025-08-14 10:36:45', '10', NULL),
(207, 'avocado', 'completed', 'avocado', ',water,sugar', '', '2025-08-14 10:38:08', '10', NULL),
(208, 'shawarma, pizza', 'completed', 'chicken,pita-bread,, pizza-dough,mozzerella,', 'onion,romaine, olives,tomatoes', '', '2025-08-14 10:39:27', '10', NULL),
(209, 'Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito', 'completed', 'vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable', 'carrot, carrot, carrot, carrot, carrot, carrot, carrot', '', '2025-08-14 10:42:48', '10', NULL),
(210, 'pinaple, ice coffee, pizza', 'completed', 'pinaple,, warm water,coffee granules,sugar,, pizza-dough,mozzerella,', 'water,suger, ice,cold milk, olives,tomatoes', '', '2025-08-14 10:53:16', '10', NULL),
(211, 'white-forest, white-forest', 'completed', 'butter,flour, butter,flour', 'eggs,corn-starch, eggs,corn-starch', '', '2025-08-14 11:14:29', '10', NULL),
(212, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-14 11:16:51', '10', NULL),
(213, 'vegetable-rice', 'completed', 'vegetables,rice,', 'onion,carrot', '', '2025-08-14 14:22:52', '10', NULL),
(214, 'strawbery', 'completed', 'strawbery,', 'water,sugar', '', '2025-08-14 14:41:39', '10', NULL),
(215, 'tea, ice coffee', 'completed', 'tea plant,, warm water,coffee granules,sugar,', 'water, ice,cold milk', '', '2025-08-15 11:40:23', '13', '2025-08-15 11:45:38'),
(216, 'black-forest, black-forest, black-forest, black-forest, black-forest, white-forest, white-forest, white-forest, white-forest, white-forest', 'completed', 'cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, butter,flour, butter,flour, butter,flour, butter,f', 'butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, eggs,corn-starch, eggs,corn-st', '', '2025-08-15 11:46:10', '100', NULL),
(217, 'ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee, ice coffee', 'completed', 'warm water,coffee granules,sugar,, warm water,coffee granules,sugar,, warm water,coffee granules,sugar,, warm water,coff', 'ice,cold milk, ice,cold milk, ice,cold milk, ice,cold milk, ice,cold milk, ice,cold milk, ice,cold milk, ice,cold milk, ', '', '2025-08-15 11:49:08', '260', NULL),
(218, 'Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito', 'completed', 'vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable,', 'carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, ', '', '2025-08-15 13:28:54', '1000', NULL),
(219, 'black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, black-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest, white-forest', 'completed', 'cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-powder, cocoa-po', 'butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,eggs, butter-milk,', '', '2025-08-15 13:38:56', '5000', NULL),
(220, 'kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, kitfo, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, shawarma, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza, pizza', 'completed', 'meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, meat, ', 'kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, kocho, k', '', '2025-08-15 13:45:17', '8280', NULL),
(221, 'pizza', 'completed', 'pizza-dough,mozzerella,', 'olives,tomatoes', '', '2025-08-15 14:00:13', '10', NULL),
(222, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-15 14:08:11', '10', NULL),
(223, 'pizza', 'completed', 'pizza-dough,mozzerella,', 'olives,tomatoes', '', '2025-08-15 14:47:15', '10', NULL),
(224, 'shawarma', 'completed', 'chicken,pita-bread,', 'onion,romaine', '', '2025-08-16 05:35:43', '10', NULL),
(225, 'white-forest', 'completed', 'butter,flour', 'eggs,corn-starch', '', '2025-08-16 06:05:31', '10', NULL),
(226, 'vegetable-rice', 'completed', 'vegetables,rice,', 'onion,carrot', '', '2025-08-17 08:27:42', '10', NULL),
(227, 'tea, white-forest', 'completed', 'tea plant,, butter,flour', 'water, eggs,corn-starch', '', '2025-08-17 08:59:16', '13', NULL),
(228, 'shawarma', 'completed', 'chicken,pita-bread,', 'onion,romaine', '', '2025-08-17 09:19:53', '10', NULL),
(229, 'white-forest, white-forest', 'completed', 'butter,flour, butter,flour', 'eggs,corn-starch, eggs,corn-starch', '', '2025-08-17 09:31:17', '20', NULL),
(230, 'black-forest', 'completed', 'cocoa-powder', 'butter-milk,eggs', '', '2025-08-17 11:13:45', '10', NULL),
(231, 'Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito, Borrito', 'completed', 'vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable, vegitable,', 'carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, carrot, ', '', '2025-08-19 14:10:10', '10000', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(15) NOT NULL,
  `department` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `department`) VALUES
(7, 'qwertyuiop', '$2y$10$f5gw0o81', ''),
(8, 'samuel', '$2y$10$2YvkM56P', ''),
(9, 'leul', '$2y$10$iUfZoBtA', ''),
(10, 'abebe', '$2y$10$xq/n8Q/D', '');

-- --------------------------------------------------------

--
-- Table structure for table `waiters_orders`
--

CREATE TABLE `waiters_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `waiter_id` int(11) DEFAULT NULL,
  `status` enum('pending','ready','served') DEFAULT 'pending',
  `served_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waiters_orders`
--

INSERT INTO `waiters_orders` (`id`, `order_id`, `waiter_id`, `status`, `served_at`) VALUES
(1, 201, 1, 'served', '2025-08-13 10:47:00'),
(2, 200, 1, 'served', '2025-08-13 10:47:07'),
(3, 199, 1, 'served', '2025-08-13 10:47:34'),
(4, 203, 1, 'served', '2025-08-13 10:48:13');

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `username_idx` (`username`);

--
-- Indexes for table `waiters_orders`
--
ALTER TABLE `waiters_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `finance_orders`
--
ALTER TABLE `finance_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=474;

--
-- AUTO_INCREMENT for table `food_drink`
--
ALTER TABLE `food_drink`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `waiters_orders`
--
ALTER TABLE `waiters_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `waiters_orders`
--
ALTER TABLE `waiters_orders`
  ADD CONSTRAINT `waiters_orders_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
