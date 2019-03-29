CREATE TABLE IF NOT EXISTS BF_Roles
(
  id BINARY(16) NOT NULL PRIMARY KEY,
  role VARCHAR(20) NOT NULL,
  CONSTRAINT bf_roles_id_index
    UNIQUE (id),
  CONSTRAINT bf_roles_role_index
    UNIQUE (role)
)
ENGINE InnoDB;

CREATE TABLE IF NOT EXISTS BF_Status
(
  id BINARY(16) NOT NULL PRIMARY KEY,
  status VARCHAR(20) NOT NULL,
  CONSTRAINT bf_status_id_index
    UNIQUE (id),
  CONSTRAINT bf_status_status_index
    UNIQUE (status)
)
ENGINE InnoDB;

CREATE TABLE IF NOT EXISTS BF_Users
(
  id BINARY(16) NOT NULL PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  displayName VARCHAR(20) NOT NULL,
  role BINARY(16) NOT NULL,
  status BINARY(16) NOT NULL,
  CONSTRAINT bf_users_id_index
    UNIQUE (id),
  CONSTRAINT bf_users_username_index
    UNIQUE (username),
  CONSTRAINT bf_users_displayName_index
    UNIQUE (displayName),
  CONSTRAINT bf_users_bf_roles_fk
  FOREIGN KEY (role) REFERENCES BF_Roles (id),
  CONSTRAINT bf_users_bf_status_fk
  FOREIGN KEY (status) REFERENCES BF_Status (id)
)
ENGINE InnoDB;