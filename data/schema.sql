CREATE  TABLE IF NOT EXISTS `user_role` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` VARCHAR(255) NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `parent_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_role` (`role_id` ASC),
  INDEX `parent_id_fk` (`parent_id` ASC),
  CONSTRAINT `parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `user_role` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

CREATE  TABLE IF NOT EXISTS `user_role_linker` (
  `user_id` INT(11) NOT NULL,
  `role_id` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  INDEX `role_id_fk` (`role_id` ASC),
  INDEX `user_id_fk` (`user_id` ASC),
  CONSTRAINT `role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;
