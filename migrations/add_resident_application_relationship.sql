START TRANSACTION;

ALTER TABLE `resident_information`
  MODIFY `middlename` varchar(100) DEFAULT NULL,
  MODIFY `age` tinyint(3) DEFAULT NULL,
  MODIFY `gender` enum('Male','Female','Other') DEFAULT NULL,
  MODIFY `contact` varchar(11) DEFAULT NULL,
  MODIFY `address` varchar(500) DEFAULT NULL;

ALTER TABLE `application`
  MODIFY `middlename` varchar(100) DEFAULT NULL,
  ADD COLUMN `resident_id` int(11) DEFAULT NULL,
  ADD COLUMN `application_date` datetime NOT NULL DEFAULT current_timestamp(),
  ADD KEY `application_resident_id` (`resident_id`);

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
