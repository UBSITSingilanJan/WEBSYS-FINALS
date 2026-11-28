ALTER TABLE `resident_information`
  ADD COLUMN `residency_type` ENUM('Permanent Resident', 'Temporary Resident') DEFAULT NULL AFTER `birthdate`;
