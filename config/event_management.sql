-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2024 at 12:37 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `num_seats` int(11) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `mpesa_code` varchar(255) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `event_id`, `user_id`, `num_seats`, `booking_date`, `amount`, `mpesa_code`, `approval_status`) VALUES
(15, 7, 3, 0, '2024-11-19 02:21:40', 3500.00, 'wwerWREW', 'approved'),
(16, 7, 24, 40, '2024-11-23 23:01:47', 2100.00, '33333333qw', 'approved'),
(17, 9, 24, 0, '2024-11-23 23:03:11', 400.00, '33333333qw', 'approved'),
(18, 10, 24, 5, '2024-11-24 04:44:29', 150.00, 'eeeeeeeeeeet', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int(10) NOT NULL,
  `cost` int(20) NOT NULL,
  `booking_dateline` date NOT NULL,
  `description` text DEFAULT NULL,
  `event_image` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `date`, `location`, `capacity`, `cost`, `booking_dateline`, `description`, `event_image`, `created_at`) VALUES
(5, 'Koito', '2024-11-22', 'Savanaah', 40, 0, '0000-00-00', 'attending is free', '1730744427941.gif', '2024-11-17 00:18:27'),
(7, 'wedding', '2024-11-30', 'Narok', 40, 700, '2024-11-27', 'attend', '2.1.jpg', '2024-11-18 08:08:12'),
(9, 'Graduation', '2024-11-29', 'Nairobi', 20, 100, '2024-11-27', 'Hillary Graduation', 'IMG_20210506_112926_052.jpg', '2024-11-23 18:43:45'),
(10, 'Rurasio', '2024-12-07', 'Narok', 200, 30, '2024-11-28', 'will be attending', 'medicine.png', '2024-11-24 00:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'hilla', 'hilla45@gmail.com', 'hilla', 'admin', '2024-11-16 21:21:49'),
(3, 'caren', 'Hillary@gmail.com', '$2y$10$rzHwjAUje2ECd1zEzMoXc.Rn6fu/44IyAqdB9IXCWJlczNprzUHMu', 'user', '2024-11-16 21:29:01'),
(7, 'kip', 'kip6@gmail.com', '$2y$10$.PcSorBdBPoROCAVdoznDeemjq5jewX.qJs2W/59kyK4MT6fUUj7K', 'admin', '2024-11-16 21:54:47'),
(11, 'elvis', 'elic@gmail.com', '$2y$10$iMCi88NdMqKRt7wsDo1ncOTVYj/6Xvam6NPowrGqKirJSw7bTaTgq', 'user', '2024-11-17 00:24:44'),
(12, 'Hillary', 'hill@gmail.com', '$2y$10$AG1MZ3yxPVvmBH9J4zC2S.XbDLc9Q8KOkracyaUe5WvMBJxvNp0zC', 'user', '2024-11-17 00:25:31'),
(13, 'kibet', 'kib@gmail.com', '$2y$10$2WvzODtrZnBbMIBYIDOZxeKL4u8yDyxmhhigzzuPKDnewTyGhUCaK', 'user', '2024-11-17 00:32:32'),
(24, 'elvi', 'elvisr@gmail.com', '$2y$10$SHvRegk9Z5PbdW2ucvJ.Gu/UpPrMh5lvV3Tw5F0g.GhNHxC2U10e.', 'user', '2024-11-23 20:00:56'),
(25, 'shan', 'shan@gmail.com', '$2y$10$.U3BGhv3adr5KTzpQOW5I.Z7GaLwVqjyybzncrijCCu7uv4jF5qve', 'user', '2024-11-23 22:54:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`user_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
