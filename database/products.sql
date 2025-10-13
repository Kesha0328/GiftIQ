-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2025 at 07:49 AM
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
-- Database: `gift_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `collection_id`, `created_at`) VALUES
(9, 'Birthday Bliss', 1299.00, 'Includes chocolates, mug, greeting card', '1.jpeg', 1, '2025-09-29 18:46:16'),
(10, 'Sweet Surprise', 1499.00, 'Assorted sweets, plush toy, birthday badge', '2.jpeg', 1, '2025-09-29 18:46:53'),
(11, 'Elegant Love', 1599.00, 'Rose bouquet, chocolates, keepsake box', '5.jpeg', 2, '2025-09-29 18:47:47'),
(12, 'Romantic Treat', 1999.00, 'Wine glass set, gourmet snacks, love card', '6.jpeg', 2, '2025-09-29 18:48:39'),
(13, 'Festive Cheer', 1499.00, 'Dry fruits, diyas, festive sweets', '9.jpeg', 4, '2025-09-29 18:49:48'),
(14, 'Joyful Basket', 1799.00, 'Assorted snacks, candles, festival card', '10.jpeg', 4, '2025-09-29 18:50:32'),
(15, 'Executive Box', 1999.00, 'Desk organizer, premium pen, snacks', '1.jpeg', 3, '2025-09-29 18:51:10'),
(16, 'Team Treat', 2299.00, 'Coffee mug, cookies, thank you card', '2.jpeg', 3, '2025-09-29 18:51:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collection_id` (`collection_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
