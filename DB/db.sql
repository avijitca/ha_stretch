-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 02:40 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lending`
--

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `lender_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `loan_amount` decimal(11,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `duration_years` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `status` enum('active','completed','defaulted') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `lender_id`, `borrower_id`, `loan_amount`, `interest_rate`, `duration_years`, `start_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, '50000.00', '12.70', 15, '2024-06-28', 'completed', '2024-11-12 01:36:49', '2024-11-15 21:05:43'),
(3, 2, 6, '15000.00', '10.00', 10, '2024-10-20', 'defaulted', '2024-11-12 01:50:09', '2024-11-12 12:27:00'),
(4, 1, 6, '20000.00', '9.12', 8, '2024-09-21', 'completed', '2024-11-12 01:51:02', '2024-11-13 08:47:58'),
(5, 2, 4, '10000.00', '6.00', 9, '2024-08-09', 'active', '2024-11-12 01:52:01', '2024-11-12 20:58:44'),
(6, 1, 3, '55000.00', '12.40', 20, '2024-12-15', 'active', '2024-11-12 01:53:15', '2024-11-13 08:45:05'),
(7, 2, 4, '75000.00', '16.40', 12, '2024-08-15', 'active', '2024-11-12 01:54:50', '2024-11-16 09:42:58'),
(8, 2, 4, '15000.00', '10.00', 10, '2024-10-20', 'active', '2024-11-12 02:31:21', '2024-11-12 12:31:25'),
(14, 1, 3, '25000.00', '14.80', 15, '2024-12-10', 'active', '2024-11-13 02:24:13', '0000-00-00 00:00:00'),
(15, 1, 4, '60000.00', '12.80', 12, '2024-12-15', 'active', '2024-11-16 01:45:48', '0000-00-00 00:00:00'),
(16, 5, 3, '80000.00', '16.40', 18, '2024-07-21', 'active', '2024-11-16 01:54:45', '0000-00-00 00:00:00'),
(17, 2, 4, '85000.00', '12.60', 15, '2024-08-29', 'active', '2024-11-16 15:56:38', '0000-00-00 00:00:00'),
(18, 1, 6, '25000.00', '11.40', 5, '2024-10-15', 'active', '2024-11-16 15:57:35', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` enum('lender','borrower') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'john_doe@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'lender', '2024-11-11 00:54:24', NULL),
(2, 'Chris Anderson', 'chris_anderson@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'lender', '2024-11-11 00:54:24', NULL),
(3, 'sameer Patil', 'sameer_patil@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'borrower', '2024-11-13 00:05:45', NULL),
(4, 'Amol Sen', 'amol_sen@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'borrower', '2024-11-13 00:05:45', NULL),
(5, 'Hrishikesh Das', 'hrishikesh_das@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'lender', '2024-11-13 00:09:30', NULL),
(6, 'Dolon Dhor', 'dolon_dhor@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'borrower', '2024-11-13 00:11:04', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
