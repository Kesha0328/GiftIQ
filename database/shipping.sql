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
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `order_id`, `name`, `phone`, `address`, `city`, `postal_code`, `country`, `created_at`) VALUES
(1, 2, 'DENISH BHAVESHBHAI SALIYA', '9313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-28 18:07:30'),
(2, 3, 'DENISH BHAVESHBHAI SALIYA', '9313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-28 18:10:53'),
(3, 4, 'DENISH BHAVESHBHAI SALIYA', '9313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-28 19:08:21'),
(4, 5, 'DENISH BHAVESHBHAI SALIYA', '9313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-29 03:35:27'),
(5, 6, 'DENISH BHAVESHBHAI SALIYA', '9313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-29 14:13:48'),
(6, 8, 'DENISH BHAVESHBHAI SALIYA', '09313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-29 18:19:56'),
(7, 9, 'DENISH BHAVESHBHAI SALIYA', '09313762547', '202, SHYAM VILLA FLATS. NEAR PATEL MOTERS, SINGANPRO-CAUSEWAY ROAD, KATARGAM, SURAT.', 'SURAT', '395004', 'India', '2025-09-29 18:33:41'),
(8, 10, 'JAY PARMAR', '1234567890', 'katargam', 'surat', '395004', 'india', '2025-09-29 18:54:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
