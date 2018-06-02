CREATE OR REPLACE PROCEDURE prc_export_table(p_table_name varchar2, p_file_name varchar2, p_file_type varchar2, p_export_type varchar2)
AS
invalid_export_type EXCEPTION;
PRAGMA EXCEPTION_INIT(invalid_export_type, -20001);
invalid_file_type EXCEPTION;
PRAGMA EXCEPTION_INIT(invalid_file_type, -20002);
invalid_table_name_length EXCEPTION;
PRAGMA EXCEPTION_INIT(invalid_table_name_length, -20003);
invalid_file_name_length EXCEPTION;
PRAGMA EXCEPTION_INIT(invalid_file_name_length, -20004);
invalid_file_name EXCEPTION;
PRAGMA EXCEPTION_INIT(invalid_file_name, -20005);
empty_table EXCEPTION;
PRAGMA EXCEPTION_INIT(empty_table, -20006);

v_file_name varchar2(69) := '';
v_file UTL_FILE.FILE_TYPE;

v_row_count NUMBER(6) := 0;
TYPE COLL_ARRAY IS VARRAY(128) OF VARCHAR2(24);


/

BEGIN
  CASE
    WHEN (lower(p_export_type) != 'full export') THEN raise invalid_export_type;
    WHEN (lower(p_file_type) != 'xml') THEN raise invalid_file_type;
    WHEN (length(p_table_name) > 24) THEN raise invalid_table_name_length;
    WHEN (length(p_file_name) > 64) THEN raise invalid_file_name_length;
    WHEN p_file_name LIKE '%.csv' THEN raise invalid_file_name;
    WHEN p_file_name LIKE '%.html' THEN raise invalid_file_name;
    WHEN p_file_name LIKE '%.txt' THEN raise invalid_file_name;
    WHEN p_file_name LIKE '%.xml' THEN raise invalid_file_name;
    ELSE v_file_name := TO_CHAR(p_file_name) || '.' || p_file_type;
  END CASE;
  DBMS_OUTPUT.PUT_LINE(v_file_name);
  v_file := UTL_FILE.FOPEN('REPORTSEXPORTTEST', 'test.xml', 'W');
  UTL_FILE.PUT_LINE(v_file, '<?xml version="1.0" encoding="utf-8"?>');
  UTL_FILE.FCLOSE(v_file);
  
  EXECUTE IMMEDIATE 'SELECT COUNT(*) FROM ' ||  p_table_name into v_row_count;
  CASE
    WHEN (v_row_count = 0) THEN raise empty_table;
    ELSE null;
  END CASE;
  
  
  EXCEPTION
  WHEN invalid_export_type THEN
    raise_application_error(-20001, 'Currently only export mode is supported! Export type needs to be "full export"!');  
  WHEN invalid_file_type THEN
    raise_application_error(-20002, 'Currently we can only export into XML format! File type parameter needs to be "xml"!');
  WHEN invalid_table_name_length THEN
    raise_application_error(-20003, 'Table name length is ' || length(p_table_name) || '! Max table name length needs to be 24!');
  WHEN invalid_file_name_length THEN
    raise_application_error(-20004, 'File name length is ' || length(p_file_name) || '! Max file name length needs to be 64!');
  WHEN invalid_file_name THEN
    raise_application_error(-20005, 'File name length is ' || p_file_name || '! File name should not include an extension!');
  WHEN empty_table THEN
    DBMS_OUTPUT.PUT_LINE('The selected table is empty!');
  WHEN OTHERS THEN
    raise;
END prc_export_table;
/
SET SERVEROUTPUT ON;
DECLARE
  table_name VARCHAR2(24) := 'BlaBla';
BEGIN
  DBMS_OUTPUT.PUT_LINE(table_name);
  prc_export_table('TEST', 'test', 'xml' , 'full export');
  
END;