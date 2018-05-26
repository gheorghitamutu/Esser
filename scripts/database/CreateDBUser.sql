CREATE OR REPLACE PROCEDURE prc_esser_crt_root_user(p_username VARCHAR2, p_password VARCHAR2)
AS
BEGIN
  -- USER SQL
  EXECUTE_IMMEDIATE('CREATE USER ' || p_username || 'IDENTIFIED BY ' || p_password);
  
  -- QUOTAS
  ALTER USER p_username QUOTA UNLIMITED ON USERS;
  
  -- ROLES
  GRANT "CONNECT" TO p_username ;
  ALTER USER p_username DEFAULT ROLE "CONNECT";
  
  -- SYSTEM PRIVILEGES
  GRANT CREATE LIBRARY TO p_username ;
  GRANT CREATE TRIGGER TO p_username ;
  GRANT CREATE ANY DIRECTORY TO p_username ;
  GRANT CREATE MATERIALIZED VIEW TO p_username ;
  GRANT CREATE INDEXTYPE TO p_username ;
  GRANT CREATE VIEW TO p_username ;
  GRANT CREATE SESSION TO p_username ;
  GRANT CREATE RULE TO p_username ;
  GRANT CREATE TABLE TO p_username ;
  GRANT CREATE TYPE TO p_username ;
  GRANT CREATE TABLESPACE TO p_username ;
  GRANT CREATE SYNONYM TO p_username ;
  GRANT CREATE SEQUENCE TO p_username ;
  GRANT CREATE RULE SET TO p_username ;
  GRANT CREATE PROCEDURE TO p_username ;
  GRANT EXECUTE ON UTL_FILE TO p_username;
END prc_esser_crt_root_user;
