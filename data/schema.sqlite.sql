CREATE TABLE IF NOT EXISTS `user_role` (
  `role_id` VARCHAR(255) NOT NULL,
  `is_default` TINYINT(1) NOT NULL,
  `parent` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
);

CREATE TABLE IF NOT EXISTS `user_role_linker` (
  `user_id` INTEGER NOT NULL,
  `role_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
);

