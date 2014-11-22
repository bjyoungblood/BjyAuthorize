CREATE  TABLE IF NOT EXISTS `user_role` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` VARCHAR(255) NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `parent_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_role` (`role_id` ASC),
  INDEX `idx_parent_id` (`parent_id` ASC),
  CONSTRAINT `fk_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `user_role` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

CREATE  TABLE IF NOT EXISTS `user_role_linker` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `role_id` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  INDEX `idx_role_id` (`role_id` ASC),
  INDEX `idx_user_id` (`user_id` ASC),
  CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;
