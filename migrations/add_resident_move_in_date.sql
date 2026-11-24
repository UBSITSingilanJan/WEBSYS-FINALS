-- Run once on an existing installation. Enter verified move-in dates for existing residents in Manage Residents.
ALTER TABLE `resident_information`
  ADD COLUMN `move_in_date` date DEFAULT NULL;
