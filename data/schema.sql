CREATE TABLE IF NOT EXISTS user_role (
  role_id varchar(32) CHARACTER SET latin1 NOT NULL,
  is_default tinyint(1) NOT NULL,
  parent varchar(32) CHARACTER SET latin1  DEFAULT NULL,
  PRIMARY KEY (role_id),
  FOREIGN KEY parent_role (parent) REFERENCES user_role(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS user_role_linker (
  user_id int(11) unsigned NOT NULL,
  role_id varchar(32) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (user_id, role_id),
  FOREIGN KEY role (role_id) REFERENCES user_role(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
