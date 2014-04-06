CREATE TABLE IF NOT EXISTS user_role (
  id serial  not null,
  roleId character varying(255) NOT NULL,
  is_default smallint NOT NULL,
  parent_id character varying(255) DEFAULT NULL,
  CONSTRAINT user_role_pkey 	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS user_role_linker (
  user_id integer NOT NULL,
  role_id integer NOT NULL
);
