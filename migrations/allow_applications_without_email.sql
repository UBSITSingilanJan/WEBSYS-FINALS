-- Run once on an existing installation. Back up the database first.
ALTER TABLE `application`
  MODIFY `email` varchar(100) DEFAULT NULL;
