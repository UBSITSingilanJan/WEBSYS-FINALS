-- =====================================================================
-- Financial Aid System - FRESH INSTALL schema (v4: data types / NOT NULL revised)
-- Target DBMS : MariaDB 10.11 / MySQL 8 (InnoDB, utf8mb4)
-- Import into phpMyAdmin or: mysql -u root < financialaidsystem-v4.sql
--
-- WARNING: this script DROPS and re-creates every table so it can be
-- re-run without errors. Do NOT run it on a database holding real data.
--
-- Field-design rules applied (IMDBSE2 - Physical Database Design):
--   * smallest data type that fits the domain (TINYINT, not INT, etc.)
--   * INT for surrogate keys
--   * VARCHAR(n) sized to real-life values, never a blanket VARCHAR(255)
--   * ENUM for status / category columns
--   * DECIMAL for money, DATE/DATETIME/TIMESTAMP for dates
--   * indexes only on PKs, FKs and columns the PHP code filters/sorts by
--   * real FOREIGN KEYs so the DBMS enforces integrity
--
-- Column sizes are matched to what the PHP code can actually insert, and
-- columns that receive '' from the forms (application table) stay
-- VARCHAR, so existing PHP code runs unchanged.
-- =====================================================================

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `financialaidsystem-new`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `financialaidsystem-new`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `assistance_release`;
DROP TABLE IF EXISTS `application_document`;
DROP TABLE IF EXISTS `application`;
DROP TABLE IF EXISTS `assistance_program`;
DROP TABLE IF EXISTS `resident_information`;
DROP TABLE IF EXISTS `user`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- user : login accounts (residents and barangay workers)
-- ---------------------------------------------------------------------
CREATE TABLE `user` (
  `user_id`   INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(50)  NOT NULL,
  `lastname`  VARCHAR(50)  NOT NULL,
  `email`     VARCHAR(100) NOT NULL,
  `password`  VARCHAR(255) NOT NULL,              -- holds a bcrypt hash (60 chars) or legacy plain text
  `contact`   VARCHAR(20)  DEFAULT NULL,
  `address`   VARCHAR(100) DEFAULT NULL,
  `position`  ENUM('Barangay Captain','Kagawad','Secretary') DEFAULT NULL,
  `role`      ENUM('resident','worker') NOT NULL DEFAULT 'resident',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unique_email` (`email`),
  KEY `idx_user_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- resident_information : barangay masterlist
-- (PHP converts empty form values to NULL for this table, so ENUM /
--  numeric columns are safe here)
-- ---------------------------------------------------------------------
CREATE TABLE `resident_information` (
  `resident_id`       INT NOT NULL AUTO_INCREMENT,
  `user_id`           INT DEFAULT NULL,
  `firstname`         VARCHAR(50)  NOT NULL,
  `middlename`        VARCHAR(50)  DEFAULT NULL,
  `lastname`          VARCHAR(50)  NOT NULL,
  `suffix`            VARCHAR(10)  DEFAULT NULL,
  `nickname`          VARCHAR(50)  DEFAULT NULL,
  `birthdate`         DATE DEFAULT NULL,
  `move_in_date`      DATE DEFAULT NULL,
  `age`               TINYINT UNSIGNED DEFAULT NULL,      -- 0-255 is enough for an age
  `gender`            ENUM('Male','Female','Other') DEFAULT NULL,
  `civilstatus`       ENUM('Single','Married','Widowed','Annulled') DEFAULT NULL,
  `contact`           VARCHAR(20)  DEFAULT NULL,
  `email`             VARCHAR(100) DEFAULT NULL,
  `housenumber`       VARCHAR(20)  DEFAULT NULL,
  `street`            VARCHAR(60) DEFAULT NULL,
  `barangay`          VARCHAR(50) DEFAULT NULL,
  `city`              VARCHAR(50) DEFAULT NULL,
  `province`          VARCHAR(50) DEFAULT NULL,
  `zipcode`           CHAR(4)      DEFAULT NULL,           -- PH zip codes are always 4 digits
  `address`           VARCHAR(300) DEFAULT NULL,          -- combined address built by PHP
  `birthplace`        VARCHAR(60) DEFAULT NULL,
  `occupation`        VARCHAR(60) DEFAULT NULL,
  `monthly_income`    DECIMAL(10,2) DEFAULT NULL,
  `education`         ENUM('Elementary','High School','Senior High School','College','Graduate','Vocational') DEFAULT NULL,
  `course`            VARCHAR(100) DEFAULT NULL,
  `talents`           TEXT DEFAULT NULL,
  `marketable_skills` TEXT DEFAULT NULL,
  `health_condition`  TEXT DEFAULT NULL,
  `transport_mode`    ENUM('Public transportation','Personal vehicle','Company service','Walking','Other') DEFAULT NULL,
  `transport_details` VARCHAR(100) DEFAULT NULL,
  `soloparent`        ENUM('Yes','No') DEFAULT NULL,
  `children`          TINYINT UNSIGNED DEFAULT NULL,      -- PHP allows 0-99
  `member`            ENUM('Yes','No') DEFAULT NULL,
  `program`           VARCHAR(100) DEFAULT NULL,
  `dependent`         ENUM('Yes','No') DEFAULT NULL,
  `relationship`      VARCHAR(50) DEFAULT NULL,
  `benefits`          VARCHAR(100) DEFAULT NULL,          -- JSON list of up to 7 short labels (~55 chars max)
  `school`            VARCHAR(100) DEFAULT NULL,
  `yearlevel`         VARCHAR(30)  DEFAULT NULL,
  `enrolled`          VARCHAR(20)  DEFAULT NULL,
  `attained`          VARCHAR(30) DEFAULT NULL,
  `senior`            VARCHAR(150) DEFAULT NULL,          -- JSON list of up to 8 short labels (~85 chars max)
  `issues`            TEXT DEFAULT NULL,
  PRIMARY KEY (`resident_id`),
  UNIQUE KEY `resident_information_user_id` (`user_id`),
  KEY `idx_resident_name` (`lastname`, `firstname`),      -- ORDER BY lastname, firstname + name search
  CONSTRAINT `resident_information_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- assistance_program : aid programs workers can open/close
-- ---------------------------------------------------------------------
CREATE TABLE `assistance_program` (
  `program_id`   INT NOT NULL AUTO_INCREMENT,
  `program_name` VARCHAR(150) NOT NULL,
  `description`  TEXT NOT NULL,
  `status`       ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by`   INT DEFAULT NULL,
  PRIMARY KEY (`program_id`),
  UNIQUE KEY `unique_program_name` (`program_name`),
  KEY `idx_program_created_by` (`created_by`),
  CONSTRAINT `assistance_program_user_fk`
    FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- application : aid requests
-- NOTE: PHP must send NULL (not '') for blank ENUM/TINYINT answers here
-- ---------------------------------------------------------------------
CREATE TABLE `application` (
  `applicant_id`     INT NOT NULL AUTO_INCREMENT,
  `resident_id`      INT DEFAULT NULL,
  `user_id`          INT DEFAULT NULL,
  `application_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status`           ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `aid_type`         VARCHAR(150) NOT NULL,             -- an assistance_program name
  `firstname`        VARCHAR(50)  NOT NULL,
  `middlename`       VARCHAR(50)  DEFAULT NULL,
  `lastname`         VARCHAR(50)  NOT NULL,
  `suffix`           VARCHAR(10)  DEFAULT NULL,
  `email`            VARCHAR(100) DEFAULT NULL,
  `contact`          VARCHAR(20)  DEFAULT NULL,
  `gender`           ENUM('Male','Female','Other') DEFAULT NULL,
  `civilstatus`      ENUM('Single','Married','Widowed','Annulled') DEFAULT NULL,
  `education`        ENUM('Elementary','High School','Senior High School','College','Graduate','Vocational') DEFAULT NULL,
  `soloparent`       ENUM('Yes','No') DEFAULT NULL,
  `children`         TINYINT UNSIGNED DEFAULT NULL,      -- 0-99
  `member`           ENUM('Yes','No') DEFAULT NULL,
  `program`          VARCHAR(100) DEFAULT NULL,
  `dependent`        ENUM('Yes','No') DEFAULT NULL,
  `relationship`     VARCHAR(50) DEFAULT NULL,
  `housenumber`      VARCHAR(20)  DEFAULT NULL,
  `street`           VARCHAR(60) DEFAULT NULL,
  `barangay`         VARCHAR(50) DEFAULT NULL,
  `city`             VARCHAR(50) DEFAULT NULL,
  `province`         VARCHAR(50) DEFAULT NULL,
  `zipcode`          CHAR(4)      DEFAULT NULL,
  `birthplace`       VARCHAR(60) DEFAULT NULL,
  `occupation`       VARCHAR(60) DEFAULT NULL,
  `school`           VARCHAR(100) DEFAULT NULL,
  `yearlevel`        VARCHAR(30)  DEFAULT NULL,
  `course`           VARCHAR(100) DEFAULT NULL,
  `benefits`         VARCHAR(100) DEFAULT NULL,          -- JSON list
  `enrolled`         VARCHAR(20)  DEFAULT NULL,
  `attained`         VARCHAR(30) DEFAULT NULL,
  `senior`           VARCHAR(150) DEFAULT NULL,          -- JSON list
  `issues`           TEXT DEFAULT NULL,
  `documents`        TEXT DEFAULT NULL,                  -- legacy JSON file paths (read by PHP for old records)
  PRIMARY KEY (`applicant_id`),
  KEY `application_user_id` (`user_id`),
  KEY `application_resident_id` (`resident_id`),
  KEY `idx_application_status` (`status`),
  KEY `idx_application_email` (`email`),
  KEY `idx_application_name` (`lastname`, `firstname`),
  CONSTRAINT `application_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
    ON DELETE SET NULL,
  CONSTRAINT `application_resident_fk`
    FOREIGN KEY (`resident_id`) REFERENCES `resident_information` (`resident_id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- application_document : uploaded proof files (PHP reads/writes BLOBs)
-- MEDIUMBLOB (16 MB) is plenty for the 5 MB upload limit in the PHP code.
-- ---------------------------------------------------------------------
CREATE TABLE `application_document` (
  `document_id`   INT NOT NULL AUTO_INCREMENT,
  `applicant_id`  INT NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `mime_type`     ENUM('image/jpeg','image/png','image/webp','application/pdf') NOT NULL,
  `file_data`     MEDIUMBLOB   NOT NULL,
  `uploaded_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `application_document_applicant_id` (`applicant_id`),
  CONSTRAINT `application_document_applicant_fk`
    FOREIGN KEY (`applicant_id`) REFERENCES `application` (`applicant_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- assistance_release : money released to approved applicants
-- ---------------------------------------------------------------------
CREATE TABLE `assistance_release` (
  `release_id`   INT NOT NULL AUTO_INCREMENT,
  `applicant_id` INT NOT NULL,
  `amount`       DECIMAL(10,2) NOT NULL,                 -- exact money type, never FLOAT
  `release_date` DATE NOT NULL,
  `remarks`      VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`release_id`),
  KEY `idx_applicant_id` (`applicant_id`),
  KEY `idx_release_date` (`release_date`),
  CONSTRAINT `assistance_release_application_fk`
    FOREIGN KEY (`applicant_id`) REFERENCES `application` (`applicant_id`)
    ON DELETE RESTRICT                                   -- never silently lose payout records
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- Seed data (parents first so every foreign key is satisfied)
-- All sample passwords below are the bcrypt hash of: 123
-- =====================================================================
INSERT INTO `user` (`user_id`, `firstname`, `lastname`, `email`, `password`, `contact`, `address`, `position`, `role`) VALUES
(1, 'Jan',      'Singilan', 'jansingilan@gmail.com',      '$2y$10$O3xCDaD8d21jFJ49AB2gjepWMrLLbQdoAyRePwnJ0d75bigqi9SOu', NULL,          NULL,          NULL,        'resident'),
(2, 'Janelle',  'Sambahon', 'janellesambahon@gmail.com',  '$2y$10$O3xCDaD8d21jFJ49AB2gjepWMrLLbQdoAyRePwnJ0d75bigqi9SOu', NULL,          NULL,          NULL,        'resident'),
(3, 'Marc',     'Soledad',  'marcsoledad@gmail.com',      '$2y$10$O3xCDaD8d21jFJ49AB2gjepWMrLLbQdoAyRePwnJ0d75bigqi9SOu', NULL,          NULL,          NULL,        'resident'),
(4, 'Sir',      'Pogi',     'sirpogi@gmail.com',          '$2y$10$O3xCDaD8d21jFJ49AB2gjepWMrLLbQdoAyRePwnJ0d75bigqi9SOu', NULL,          NULL,          NULL,        'resident'),
(5, 'Cristina', 'Peridot',  'cristinaperido@gmail.com',   '$2y$10$O3xCDaD8d21jFJ49AB2gjepWMrLLbQdoAyRePwnJ0d75bigqi9SOu', '09876754321', 'Trancoville', 'Secretary', 'worker');

INSERT INTO `resident_information`
  (`resident_id`, `user_id`, `firstname`, `middlename`, `lastname`, `age`, `gender`, `contact`, `address`, `street`) VALUES
(2, 1,    'Jan',   'B', 'Singilan', 127, 'Male', '09876754321', 'irisan', 'irisan'),
(6, 3,    'Marc',  'Q', 'Soledad',   31, 'Male', '09657152431', 'irisan', 'irisan'),
(7, NULL, 'Chris', 'T', 'Offer',     10, 'Male', '09123456789', 'Manila', 'Manila');

INSERT INTO `application`
  (`applicant_id`, `resident_id`, `user_id`, `firstname`, `middlename`, `lastname`, `email`, `aid_type`, `status`) VALUES
(6, 2, 1,    'Jan',   'B', 'Singilan', 'jansingilan@gmail.com', 'Financial Aid', 'Approved'),
(7, 7, NULL, 'Chris', 'T', 'Offer',    'christoffer@gmail.com', 'Scholarship',   'Approved');

INSERT INTO `assistance_release` (`release_id`, `applicant_id`, `amount`, `release_date`, `remarks`) VALUES
(2, 7, 45.00, '2026-11-16', NULL),
(3, 6, 45.00, '2026-11-16', NULL);

ALTER TABLE `user`                 AUTO_INCREMENT = 6;
ALTER TABLE `resident_information` AUTO_INCREMENT = 8;
ALTER TABLE `application`          AUTO_INCREMENT = 8;
ALTER TABLE `assistance_release`   AUTO_INCREMENT = 4;
