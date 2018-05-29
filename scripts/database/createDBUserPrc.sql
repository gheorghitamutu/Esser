CREATE OR REPLACE PROCEDURE prc_esser_crt_root_user(p_username VARCHAR2, p_password VARCHAR2)
AS
  v_username VARCHAR2(16);
  v_password VARCHAR2(16);
  v_sql_cmd VARCHAR2(1000);
  exc_username_length exception;
  PRAGMA EXCEPTION_INIT(exc_username_length, -20001);
  exc_password_length exception;
  PRAGMA EXCEPTION_INIT(exc_password_length, -20002);
  exc_non_alpnum_username exception;
  PRAGMA EXCEPTION_INIT(exc_non_alpnum_username, -20003);
  exc_non_alpnum_password exception;
  PRAGMA EXCEPTION_INIT(exc_non_alpnum_password, -20004);
BEGIN
  IF (length(p_username) > 16 or length(p_username) < 4) THEN
    raise exc_username_length;
  END IF;
  IF (length(p_password) > 16 or length(p_password) < 4) THEN
    raise exc_password_length;
  END IF;
  IF (REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') IS NOT NULL) THEN
    raise exc_non_alpnum_username;
  END IF;
  IF (REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') IS NOT NULL) THEN
    raise exc_non_alpnum_password;
  END IF;
  
  v_username := p_username;
  v_password := p_password;
  -- USER SQL
  v_sql_cmd := 'CREATE USER ' || v_username || ' IDENTIFIED BY ' || v_password;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
  EXECUTE IMMEDIATE(v_sql_cmd);
  
  -- QUOTAS
  v_sql_cmd := 'ALTER USER ' || v_username || ' QUOTA UNLIMITED ON USERS';
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  
  -- ROLES
  v_sql_cmd := 'GRANT "CONNECT" TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'ALTER USER ' || v_username || ' DEFAULT ROLE "CONNECT"';
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  
  -- SYSTEM PRIVILEGES
  v_sql_cmd := 'GRANT CREATE LIBRARY TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE TRIGGER TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE ANY DIRECTORY TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE MATERIALIZED VIEW TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE INDEXTYPE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE VIEW TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE SESSION TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE RULE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE TABLE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE TYPE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE TABLESPACE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE SYNONYM TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE SEQUENCE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE RULE SET TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT CREATE PROCEDURE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);
  v_sql_cmd := 'GRANT EXECUTE ON UTL_FILE TO ' || v_username;
  DBMS_OUTPUT.PUT_LINE(v_sql_cmd);
	EXECUTE IMMEDIATE(v_sql_cmd);

  EXCEPTION
  WHEN exc_username_length THEN
    raise_application_error(-20001, 'The provided username length is: ' || length(p_username) || ' !. It needs to be between 4 and 16 alpha-numeric characters!');
  WHEN exc_password_length THEN
    raise_application_error(-20002, 'The provided password length is: ' || length(p_password) || ' !. It needs to be between 4 and 16 alpha-numeric characters!');
  WHEN exc_non_alpnum_username THEN
    raise_application_error(-20003, 'Found illegal non-alpha-numeric character in the provided username: "' || REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') || '" !');
  WHEN exc_non_alpnum_password THEN
    raise_application_error(-20004, 'Found illegal non-alpha-numeric character in the provided password: "' || REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') || '" !');
  WHEN OTHERS THEN  
    IF (SQLCODE = -1920) THEN
      EXECUTE IMMEDIATE('DROP USER ' || p_username);
      DBMS_OUTPUT.PUT_LINE('A user with the name: ' || p_username || ' already existed and was dropped!');
      prc_esser_crt_root_user(p_username, p_password);
    ELSE
      RAISE;
    END IF;    
    
END prc_esser_crt_root_user;
/
BEGIN
  prc_esser_crt_root_user('EsseR', 'EsseR1234');
END;
/