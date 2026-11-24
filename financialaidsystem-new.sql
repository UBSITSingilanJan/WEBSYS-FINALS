-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2026 at 03:01 PM
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
-- Database: `financialaidsystem-new`
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
  `email` varchar(100) DEFAULT NULL,
  `aid_type` varchar(150) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `application_date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`applicant_id`, `firstname`, `middlename`, `lastname`, `email`, `aid_type`, `user_id`, `status`) VALUES
(6, 'Jan', 'B', 'Singilan', 'jansingilan@gmail.com', 'Financial Aid', 1, 'Approved'),
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
(2, 7, 45.00, '2026-11-16', NULL),
(3, 6, 45.00, '2026-11-16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assistance_program`
--

CREATE TABLE `assistance_program` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`program_id`),
  UNIQUE KEY `unique_program_name` (`program_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `application_user_id` (`user_id`),
  ADD KEY `application_resident_id` (`resident_id`);

CREATE TABLE `application_document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_data` longblob NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`),
  KEY `application_document_applicant_id` (`applicant_id`),
  CONSTRAINT `application_document_applicant_fk`
    FOREIGN KEY (`applicant_id`) REFERENCES `application` (`applicant_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `assistance_release`
--
ALTER TABLE `assistance_release`
  ADD PRIMARY KEY (`release_id`),
  ADD KEY `idx_applicant_id` (`applicant_id`);

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
-- AUTO_INCREMENT for table `assistance_release`
--
ALTER TABLE `assistance_release`
  MODIFY `release_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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

ALTER TABLE `application`
  MODIFY `aid_type` varchar(150) NOT NULL;

ALTER TABLE `application`
  MODIFY `middlename` varchar(100) DEFAULT NULL,
  ADD COLUMN `suffix` varchar(20) DEFAULT NULL,
  ADD COLUMN `contact` varchar(20) DEFAULT NULL,
  ADD COLUMN `gender` enum('Male','Female','Other') DEFAULT NULL,
  ADD COLUMN `civilstatus` varchar(50) DEFAULT NULL,
  ADD COLUMN `education` varchar(100) DEFAULT NULL,
  ADD COLUMN `soloparent` varchar(20) DEFAULT NULL,
  ADD COLUMN `children` varchar(20) DEFAULT NULL,
  ADD COLUMN `member` varchar(255) DEFAULT NULL,
  ADD COLUMN `program` varchar(255) DEFAULT NULL,
  ADD COLUMN `dependent` varchar(100) DEFAULT NULL,
  ADD COLUMN `relationship` varchar(100) DEFAULT NULL,
  ADD COLUMN `housenumber` varchar(50) DEFAULT NULL,
  ADD COLUMN `street` varchar(100) DEFAULT NULL,
  ADD COLUMN `barangay` varchar(100) DEFAULT NULL,
  ADD COLUMN `city` varchar(100) DEFAULT NULL,
  ADD COLUMN `province` varchar(100) DEFAULT NULL,
  ADD COLUMN `zipcode` varchar(20) DEFAULT NULL,
  ADD COLUMN `birthplace` varchar(100) DEFAULT NULL,
  ADD COLUMN `occupation` varchar(100) DEFAULT NULL,
  ADD COLUMN `school` varchar(150) DEFAULT NULL,
  ADD COLUMN `yearlevel` varchar(50) DEFAULT NULL,
  ADD COLUMN `course` varchar(100) DEFAULT NULL,
  ADD COLUMN `benefits` text DEFAULT NULL,
  ADD COLUMN `enrolled` varchar(50) DEFAULT NULL,
  ADD COLUMN `attained` varchar(100) DEFAULT NULL,
  ADD COLUMN `senior` varchar(50) DEFAULT NULL,
  ADD COLUMN `issues` text DEFAULT NULL;

ALTER TABLE `resident_information`
  MODIFY `firstname` varchar(100) DEFAULT NULL,
  MODIFY `middlename` varchar(100) DEFAULT NULL,
  MODIFY `lastname` varchar(100) DEFAULT NULL,
  MODIFY `age` tinyint(3) DEFAULT NULL,
  MODIFY `gender` enum('Male','Female','Other') DEFAULT NULL,
  MODIFY `contact` varchar(11) DEFAULT NULL,
  MODIFY `address` varchar(500) DEFAULT NULL,
  ADD COLUMN `user_id` int(11) DEFAULT NULL,
  ADD COLUMN `suffix` varchar(20) DEFAULT NULL,
  ADD COLUMN `email` varchar(100) DEFAULT NULL,
  ADD COLUMN `housenumber` varchar(50) DEFAULT NULL,
  ADD COLUMN `street` varchar(100) DEFAULT NULL,
  ADD COLUMN `barangay` varchar(100) DEFAULT NULL,
  ADD COLUMN `city` varchar(100) DEFAULT NULL,
  ADD COLUMN `province` varchar(100) DEFAULT NULL,
  ADD COLUMN `zipcode` varchar(20) DEFAULT NULL,
  ADD COLUMN `nickname` varchar(100) DEFAULT NULL,
  ADD COLUMN `birthdate` date DEFAULT NULL,
  ADD COLUMN `move_in_date` date DEFAULT NULL,
  ADD COLUMN `civilstatus` varchar(50) DEFAULT NULL,
  ADD COLUMN `birthplace` varchar(100) DEFAULT NULL,
  ADD COLUMN `occupation` varchar(100) DEFAULT NULL,
  ADD COLUMN `monthly_income` decimal(12,2) DEFAULT NULL,
  ADD COLUMN `education` varchar(100) DEFAULT NULL,
  ADD COLUMN `course` varchar(100) DEFAULT NULL,
  ADD COLUMN `talents` text DEFAULT NULL,
  ADD COLUMN `marketable_skills` text DEFAULT NULL,
  ADD COLUMN `health_condition` text DEFAULT NULL,
  ADD COLUMN `transport_mode` varchar(50) DEFAULT NULL,
  ADD COLUMN `transport_details` varchar(255) DEFAULT NULL,
  ADD COLUMN `soloparent` varchar(20) DEFAULT NULL,
  ADD COLUMN `children` varchar(20) DEFAULT NULL,
  ADD COLUMN `member` varchar(20) DEFAULT NULL,
  ADD COLUMN `program` varchar(255) DEFAULT NULL,
  ADD COLUMN `dependent` varchar(20) DEFAULT NULL,
  ADD COLUMN `relationship` varchar(100) DEFAULT NULL,
  ADD COLUMN `benefits` text DEFAULT NULL,
  ADD COLUMN `school` varchar(150) DEFAULT NULL,
  ADD COLUMN `yearlevel` varchar(50) DEFAULT NULL,
  ADD COLUMN `enrolled` varchar(50) DEFAULT NULL,
  ADD COLUMN `attained` varchar(100) DEFAULT NULL,
  ADD COLUMN `senior` text DEFAULT NULL,
  ADD COLUMN `issues` text DEFAULT NULL,
  ADD UNIQUE KEY `resident_information_user_id` (`user_id`);

UPDATE `resident_information` ri
JOIN `user` u
  ON LOWER(ri.`firstname`) = LOWER(u.`firstname`)
 AND LOWER(ri.`lastname`) = LOWER(u.`lastname`)
SET ri.`user_id` = u.`user_id`
WHERE ri.`user_id` IS NULL
  AND u.`role` = 'resident';

UPDATE `application` a
JOIN `resident_information` ri ON ri.`user_id` = a.`user_id`
SET a.`resident_id` = ri.`resident_id`
WHERE a.`resident_id` IS NULL
  AND a.`user_id` IS NOT NULL;

ALTER TABLE `resident_information`
  ADD CONSTRAINT `resident_information_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
    ON DELETE SET NULL;

ALTER TABLE `application`
  ADD CONSTRAINT `application_resident_fk`
    FOREIGN KEY (`resident_id`) REFERENCES `resident_information` (`resident_id`)
    ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
