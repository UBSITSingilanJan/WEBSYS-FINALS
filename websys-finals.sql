-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2026 at 03:57 PM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `websys-finals`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `applicant_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `aid_type` enum('Financial Aid','Scholarship') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`applicant_id`, `firstname`, `middlename`, `lastname`, `email`, `aid_type`, `user_id`, `status`) VALUES
(6, 'Jan', 'B', 'Singilan', 'jansingilan@gmail.com', 'Financial Aid', NULL, 'Approved'),
(7, 'Chris', 'T', 'Offer', 'christoffer@gmail.com', 'Scholarship', NULL, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `assistance_release`
--

CREATE TABLE `assistance_release` (
  `release_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `release_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assistance_release`
--

INSERT INTO `assistance_release` (`release_id`, `applicant_id`, `amount`, `release_date`, `remarks`) VALUES
(1, 7, 5000.00, '2026-11-15', 'Wag igastos'),
(2, 6, 1000.00, '2026-11-15', NULL),
(3, 7, 123.00, '2026-11-15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resident_information`
--

CREATE TABLE `resident_information` (
  `resident_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `age` tinyint(3) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `contact` varchar(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident_information`
--

INSERT INTO `resident_information` (`resident_id`, `firstname`, `middlename`, `lastname`, `age`, `gender`, `contact`, `address`, `user_id`) VALUES
(2, 'Jan', 'B', 'Singilan', 127, 'Male', '09876754321', 'irisan', NULL),
(6, 'Marc', 'Q', 'Soledad', 31, 'Male', '09657152431', 'irisan', NULL),
(7, 'Chris', 'T', 'Offer', 10, 'Male', '09123456789', 'Manila', NULL),
(8, 'Jan', 'B', 'Singilan', 127, 'Male', '09876754321', 'irisan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `firstname`, `lastname`, `email`, `password`) VALUES
(1, 'Jan', 'Singilan', 'jansingilan@gmail.com', '123'),
(2, 'Janelle', 'Sambahon', 'janellesambahon@gmail.com', '123'),
(3, 'Marc', 'Soledad', 'marcsoledad@gmail.com', '123'),
(4, 'Sir', 'Pogi', 'sirpogi@gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `worker`
--

CREATE TABLE `worker` (
  `worker_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `position` enum('Barangay Captain','Kagawad','Secretary') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worker`
--

INSERT INTO `worker` (`worker_id`, `firstname`, `lastname`, `email`, `contact`, `password`, `address`, `position`) VALUES
(6, 'Cristina', 'Peridot', 'cristinaperido@gmail.com', '09876754321', '$2y$10$UlfuIN7SnD3jPexaoxMVMOqiVH3S2VtzisX8IUCHWHGykmlv1YwGi', 'Trancoville', 'Secretary'),
(8, 'James', 'Waldo', 'waldo@gmail.com', '09877894654', '123', 'baguio', 'Secretary');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`applicant_id`);

--
-- Indexes for table `assistance_release`
--
ALTER TABLE `assistance_release`
  ADD PRIMARY KEY (`release_id`);

--
-- Indexes for table `resident_information`
--
ALTER TABLE `resident_information`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `worker`
--
ALTER TABLE `worker`
  ADD PRIMARY KEY (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assistance_release`
--
ALTER TABLE `assistance_release`
  MODIFY `release_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resident_information`
--
ALTER TABLE `resident_information`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `worker`
--
ALTER TABLE `worker`
  MODIFY `worker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
