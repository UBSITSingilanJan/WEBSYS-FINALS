-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2026 at 09:22 AM
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
-- Database: `financialaidsystem-new`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `applicant_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `application_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `aid_type` varchar(150) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civilstatus` enum('Single','Married','Widowed','Annulled') DEFAULT NULL,
  `education` enum('Elementary','High School','Senior High School','College','Graduate','Vocational') DEFAULT NULL,
  `soloparent` enum('Yes','No') DEFAULT NULL,
  `children` tinyint(3) UNSIGNED DEFAULT NULL,
  `member` enum('Yes','No') DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `dependent` enum('Yes','No') DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `housenumber` varchar(20) DEFAULT NULL,
  `street` varchar(60) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `zipcode` char(4) DEFAULT NULL,
  `birthplace` varchar(60) DEFAULT NULL,
  `occupation` varchar(60) DEFAULT NULL,
  `school` varchar(100) DEFAULT NULL,
  `yearlevel` varchar(30) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `benefits` varchar(100) DEFAULT NULL,
  `enrolled` varchar(20) DEFAULT NULL,
  `attained` varchar(30) DEFAULT NULL,
  `senior` varchar(150) DEFAULT NULL,
  `issues` text DEFAULT NULL,
  `documents` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_document`
--

CREATE TABLE `application_document` (
  `document_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `requirement_label` varchar(150) NOT NULL DEFAULT 'Supporting Document',
  `original_name` varchar(255) NOT NULL,
  `mime_type` enum('image/jpeg','image/png','image/webp','application/pdf') NOT NULL,
  `file_data` mediumblob NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assistance_program`
--

CREATE TABLE `assistance_program` (
  `program_id` int(11) NOT NULL,
  `program_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `resident_information`
--

CREATE TABLE `resident_information` (
  `resident_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `residency_type` enum('Permanent Resident','Temporary Resident') DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civilstatus` enum('Single','Married','Widowed','Annulled') DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `housenumber` varchar(20) DEFAULT NULL,
  `street` varchar(60) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `zipcode` char(4) DEFAULT NULL,
  `address` varchar(300) DEFAULT NULL,
  `birthplace` varchar(60) DEFAULT NULL,
  `occupation` varchar(60) DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `education` enum('Elementary','High School','Senior High School','College','Graduate','Vocational') DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `talents` text DEFAULT NULL,
  `marketable_skills` text DEFAULT NULL,
  `health_condition` text DEFAULT NULL,
  `transport_mode` enum('Public transportation','Personal vehicle','Company service','Walking','Other') DEFAULT NULL,
  `transport_details` varchar(100) DEFAULT NULL,
  `soloparent` enum('Yes','No') DEFAULT NULL,
  `children` tinyint(3) UNSIGNED DEFAULT NULL,
  `member` enum('Yes','No') DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `dependent` enum('Yes','No') DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `benefits` varchar(100) DEFAULT NULL,
  `school` varchar(100) DEFAULT NULL,
  `yearlevel` varchar(30) DEFAULT NULL,
  `enrolled` varchar(20) DEFAULT NULL,
  `attained` varchar(30) DEFAULT NULL,
  `senior` varchar(150) DEFAULT NULL,
  `issues` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `position` enum('Barangay Captain','Kagawad','Secretary') DEFAULT NULL,
  `role` enum('resident','worker') NOT NULL DEFAULT 'resident',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `application_user_id` (`user_id`),
  ADD KEY `application_resident_id` (`resident_id`),
  ADD KEY `idx_application_status` (`status`),
  ADD KEY `idx_application_email` (`email`),
  ADD KEY `idx_application_name` (`lastname`,`firstname`);

--
-- Indexes for table `application_document`
--
ALTER TABLE `application_document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `application_document_applicant_id` (`applicant_id`);

--
-- Indexes for table `assistance_program`
--
ALTER TABLE `assistance_program`
  ADD PRIMARY KEY (`program_id`),
  ADD UNIQUE KEY `unique_program_name` (`program_name`),
  ADD KEY `idx_program_created_by` (`created_by`);

--
-- Indexes for table `assistance_release`
--
ALTER TABLE `assistance_release`
  ADD PRIMARY KEY (`release_id`),
  ADD KEY `idx_applicant_id` (`applicant_id`),
  ADD KEY `idx_release_date` (`release_date`);

--
-- Indexes for table `resident_information`
--
ALTER TABLE `resident_information`
  ADD PRIMARY KEY (`resident_id`),
  ADD UNIQUE KEY `resident_information_user_id` (`user_id`),
  ADD KEY `idx_resident_name` (`lastname`,`firstname`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD KEY `idx_user_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `application_document`
--
ALTER TABLE `application_document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assistance_program`
--
ALTER TABLE `assistance_program`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assistance_release`
--
ALTER TABLE `assistance_release`
  MODIFY `release_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resident_information`
--
ALTER TABLE `resident_information`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_resident_fk` FOREIGN KEY (`resident_id`) REFERENCES `resident_information` (`resident_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `application_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `application_document`
--
ALTER TABLE `application_document`
  ADD CONSTRAINT `application_document_applicant_fk` FOREIGN KEY (`applicant_id`) REFERENCES `application` (`applicant_id`) ON DELETE CASCADE;

--
-- Constraints for table `assistance_program`
--
ALTER TABLE `assistance_program`
  ADD CONSTRAINT `assistance_program_user_fk` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `assistance_release`
--
ALTER TABLE `assistance_release`
  ADD CONSTRAINT `assistance_release_application_fk` FOREIGN KEY (`applicant_id`) REFERENCES `application` (`applicant_id`);

--
-- Constraints for table `resident_information`
--
ALTER TABLE `resident_information`
  ADD CONSTRAINT `resident_information_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
