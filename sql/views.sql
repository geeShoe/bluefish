CREATE OR REPLACE SQL SECURITY INVOKER VIEW get_all_user_status AS
  SELECT UuidFromBin(id) AS id, status FROM BF_Status;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW get_all_user_roles AS
  SELECT UuidFromBin(id) AS id, role FROM BF_Roles;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW get_all_user_accounts AS
  SELECT UuidFromBin(BF_Users.id) AS id, username, displayName, BR.role, BS.status
  FROM BF_Users
INNER JOIN BF_Roles BR on BF_Users.role = BR.id
INNER JOIN BF_Status BS on BF_Users.status = BS.id;
