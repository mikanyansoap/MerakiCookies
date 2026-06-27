-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 05:11 PM
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
-- Database: `meraki_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `likes` text NOT NULL,
  `improvements` text DEFAULT NULL,
  `additional` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `likes`, `improvements`, `additional`, `created_at`, `rating`) VALUES
(1, 'Sophia Peduca', 'The Classic Chocolate Chip cookies are extremely delicious and gooey!', 'More flavor options please.', 'Great website experience!', '2026-06-27 07:16:31', 5),
(2, 'Test', 'Love it', '', '', '2026-06-27 07:55:04', 5),
(4, 'Angela Reigne', 'Masarap sya, mas masarap sa cookie ko', '', '', '2026-06-27 14:08:52', 5),
(5, 'Anonymous', 'Absolutely delicious', '', '', '2026-06-27 14:10:02', 5),
(6, 'Heidi', 'inamag na pag dating dito', '', '', '2026-06-27 14:13:55', 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `order_details` text NOT NULL,
  `delivery_method` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `special_notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_phone`, `order_details`, `delivery_method`, `payment_method`, `special_notes`, `status`, `created_at`) VALUES
(1, 2, 'Sophia Peduca', '09123456789', '2x Classic Chocolate Chip, 1x Velvety Red Velvet', 'Pickup', 'GCash', 'Less sweet if possible', 'Pending', '2026-06-27 07:16:31'),
(2, NULL, 'Angela Fernandez', '91234567', '2x Velvety Red Velvet', 'Delivery', 'Maya', 'Deliver in the afternoon', 'Completed', '2026-06-27 07:16:31'),
(5, 4, 'angela reigne fernandez', '096767676767', '20x Velvety Red Velvet', 'Delivery', 'GCash', '', 'Pending', '2026-06-27 14:09:34'),
(6, 5, 'Krizelle Jacinto', '096969696969', '3x Classic Chocolate Chip', 'Pickup', 'GCash', '', 'Preparing', '2026-06-27 14:11:50'),
(7, 6, 'Heidi Benzon', '09123456789', '1x Classic Chocolate Chip, 3x Gooey S\'mores', 'Delivery', 'GCash', 'Pa dala nalang sa ###### thx', 'Ready for Pickup', '2026-06-27 14:13:34');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `date_made` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `item_name`, `quantity`, `price`, `date_made`, `created_at`) VALUES
(1, 'Classic Chocolate Chip', 6, 45.00, '2026-06-26', '2026-06-27 14:33:21'),
(2, 'Velvety Red Velvet', 20, 50.00, '2026-06-29', '2026-06-27 14:33:41'),
(3, 'Gooey S\'mores', 5, 50.00, '2026-06-24', '2026-06-27 14:34:01'),
(4, 'Classic Chocolate Chip', 5, 45.00, '2026-06-26', '2026-06-27 14:34:22'),
(5, 'Velvety Red Velvet', 0, 50.00, '2026-06-08', '2026-06-27 14:34:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `address` text DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `links` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `address`, `gender`, `links`, `created_at`) VALUES
(1, 'Admin User', 'admin@meraki-admin.com', '09762963830', '$2y$10$3EDWCgLrTB2yLeKq7hM77.EcSqgIAOaZcbf1wj4hgfbkWj/VxNwRi', 'admin', NULL, NULL, NULL, '2026-06-27 07:16:31'),
(2, 'Sophia Peduca', 'sophia@gmail.com', '09123456789', '$2y$10$wtaprvJq8ujLsHUiSLiDduxT2YnU7N/RrjmWtApeZhgbrzxPr67XG', 'customer', 'Makati City, Metro Manila', 'Female', '', '2026-06-27 07:16:31'),
(3, 'Master Admin', 'admin@meraki.com', '0000000000', '$2y$10$6/xX1Umb3KJDO3nR12pXFuFHbayGNCVD9RXMW9vmBK91cL5DfRs22', 'admin', NULL, NULL, NULL, '2026-06-27 08:14:30'),
(4, 'angela reigne fernandez', 'angela@fernandez.com', '096767676767', '$2y$10$Ex3Qehy28ZRIOPQ4kAnLiuZ8aezXAEbgTI2LseAKhSqVrY0l2oCgO', 'customer', NULL, NULL, NULL, '2026-06-27 14:07:35'),
(5, 'Krizelle Jacinto', 'krizelle@jacinto.com', '096969696969', '$2y$10$8tmA5kXDJDo4QlnRLeyE3uraoITpVJiE5kk3aVLKINYu6ZNKFZPIO', 'customer', '', '', '', '2026-06-27 14:11:28'),
(6, 'Heidi Benzon', 'heidi@benzon.com', '09123456789', '$2y$10$URAolTdK1Go/ohz8hZJag.B9BmZ6z4WtoAz6zavLyRAsWi6yMrCDu', 'customer', NULL, NULL, NULL, '2026-06-27 14:12:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
