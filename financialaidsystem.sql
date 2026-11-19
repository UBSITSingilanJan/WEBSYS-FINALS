-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 07:18 PM
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
-- Database: `financialaidsystem`
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
  `status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`applicant_id`, `firstname`, `middlename`, `lastname`, `email`, `aid_type`, `user_id`, `status`) VALUES
(6, 'Jan', 'B', 'Singilan', 'jansingilan@gmail.com', 'Financial Aid', 1, 'Pending'),
(7, 'Chris', 'T', 'Offer', 'christoffer@gmail.com', 'Scholarship', NULL, 'Pending');

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
  `address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident_information`
--

INSERT INTO `resident_information` (`resident_id`, `firstname`, `middlename`, `lastname`, `age`, `gender`, `contact`, `address`) VALUES
(2, 'Jan', 'B', 'Singilan', 127, 'Male', '09876754321', 'irisan'),
(6, 'Marc', 'Q', 'Soledad', 31, 'Male', '09657152431', 'irisan'),
(7, 'Chris', 'T', 'Offer', 10, 'Male', '09123456789', 'Manila');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(11) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `position` enum('Barangay Captain','Kagawad','Secretary') DEFAULT NULL,
  `role` enum('resident','worker') NOT NULL DEFAULT 'resident'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `firstname`, `lastname`, `email`, `password`, `contact`, `address`, `position`, `role`) VALUES
(1, 'Jan', 'Singilan', 'jansingilan@gmail.com', '123', NULL, NULL, NULL, 'resident'),
(2, 'Janelle', 'Sambahon', 'janellesambahon@gmail.com', '123', NULL, NULL, NULL, 'resident'),
(3, 'Marc', 'Soledad', 'marcsoledad@gmail.com', '123', NULL, NULL, NULL, 'resident'),
(4, 'Sir', 'Pogi', 'sirpogi@gmail.com', '123', NULL, NULL, NULL, 'resident'),
(5, 'Cristina', 'Peridot', 'cristinaperido@gmail.com', '123', '09876754321', 'Trancoville', 'Secretary', 'worker');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `application_user_id` (`user_id`);

--
-- Indexes for table `resident_information`
--
ALTER TABLE `resident_information`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `resident_information`
--
ALTER TABLE `resident_information`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
