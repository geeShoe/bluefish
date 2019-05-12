CREATE OR REPLACE PROCEDURE add_user_status(uuid BINARY(36), statusName VARCHAR(20))
BEGIN
  INSERT INTO BF_Status SET id = UuidToBin(uuid), status = statusName;
END;

CREATE OR REPLACE PROCEDURE remove_user_status(uuid BINARY(36))
BEGIN
  DELETE FROM BF_Status WHERE UuidToBin(uuid) = id;
END;

CREATE OR REPLACE PROCEDURE get_all_status_records()
BEGIN
  SELECT * FROM get_all_user_status;
END;

CREATE OR REPLACE PROCEDURE add_user_role(uuid BINARY(36), roleName VARCHAR(20))
BEGIN
  INSERT INTO BF_Roles SET id = UuidToBin(uuid), role = roleName;
END;

CREATE OR REPLACE PROCEDURE remove_user_role(uuid BINARY(36))
BEGIN
  DELETE FROM BF_Status WHERE UuidToBin(uuid) = id;
END;

CREATE OR REPLACE PROCEDURE get_all_role_records()
BEGIN
  SELECT * FROM get_all_user_roles;
END;

CREATE OR REPLACE PROCEDURE add_user_account(uuid BINARY(36), user VARCHAR(255), pass VARCHAR(255), display VARCHAR(255), roleUUID BINARY(36), statusUUID BINARY(36))
BEGIN
  INSERT INTO BF_Users SET id = UuidToBin(uuid), username = user, password = pass, displayName = display, role = UuidToBin(roleUUID), status = UuidToBin(statusUUID);
END;

CREATE OR REPLACE PROCEDURE remove_user_account(uuid BINARY(36))
BEGIN
  DELETE FROM BF_Users WHERE UuidToBin(uuid) = id;
END;

CREATE OR REPLACE PROCEDURE get_all_user_records()
BEGIN
  SELECT * FROM get_all_user_accounts;
END;

CREATE OR REPLACE PROCEDURE get_user_account_by_username(user VARCHAR(255))
BEGIN
  SELECT id, displayName, role, status FROM get_all_user_accounts WHERE BINARY username = user;
END;

CREATE OR REPLACE PROCEDURE get_user_account_by_id(uuid BINARY(36))
BEGIN
  SELECT * FROM get_all_user_accounts WHERE uuid = id;
END;

CREATE OR REPLACE PROCEDURE get_user_login_credentials(user VARCHAR(255))
BEGIN
  SELECT * FROM get_user_login WHERE BINARY user = username;
END;

CREATE OR REPLACE PROCEDURE add_role(uuid BINARY(36), name VARCHAR(255))
BEGIN
    INSERT INTO BF_Roles SET id = UuidToBin(uuid), role = name;
END;

CREATE OR REPLACE PROCEDURE get_role_by_name(name VARCHAR(50))
BEGIN
    SELECT UuidFromBin(id) as id, role FROM BF_Roles WHERE role = name;
END;

CREATE OR REPLACE PROCEDURE add_status(uuid BINARY(36), name VARCHAR(50))
BEGIN
    INSERT INTO BF_Status SET id = UuidToBin(uuid), status = name;
END;

CREATE OR REPLACE PROCEDURE get_status_by_name(name VARCHAR(50))
BEGIN
    SELECT UuidFromBin(id) as id, status FROM BF_Status WHERE status = name;
END;

CREATE OR REPLACE PROCEDURE get_status_by_id(uuid BINARY(36))
BEGIN
    SELECT UuidFromBin(id) as id, status FROM BF_Status WHERE id = UuidToBin(uuid);
END;