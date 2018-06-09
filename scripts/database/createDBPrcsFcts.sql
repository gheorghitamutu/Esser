-- Creation of the database; Creation of DB procedures, functions and the rest of the lot (STEP 6);
SET SERVEROUTPUT ON;
CREATE OR REPLACE PROCEDURE prc_addMainRoots(p_username VARCHAR2, p_password VARCHAR2, p_email VARCHAR2, p_rootadmgrp VARCHAR2, p_rootmnggrp VARCHAR2, p_rootusrsgrp VARCHAR2)
AS
  v_usernamae VARCHAR2(16);
  v_password VARCHAR2(16);
  v_email VARCHAR2(48);
  v_userImage VARCHAR2(256);
  v_sql_cmd VARCHAR2(2000);
  v_result BOOLEAN;
  v_rootadmgrp VARCHAR2(16);
  v_rootmnggrp VARCHAR2(16);
  v_rootusrsgrp VARCHAR2(16);
  exc_username_length exception;
  exc_password_length exception;
  exc_non_alpnum_username exception;
  exc_non_alpnum_password exception;
  exc_email_length exception;
  exc_bad_email_format exception;
  PRAGMA EXCEPTION_INIT(exc_username_length, -20001);
  PRAGMA EXCEPTION_INIT(exc_password_length, -20002);
  PRAGMA EXCEPTION_INIT(exc_non_alpnum_username, -20003);
  PRAGMA EXCEPTION_INIT(exc_non_alpnum_password, -20004);
  PRAGMA EXCEPTION_INIT(exc_email_length, -20005);
  PRAGMA EXCEPTION_INIT(exc_bad_email_format, -20006);
BEGIN --
  IF (length(p_username) > 16 or length(p_username) < 4) THEN raise exc_username_length; END IF;
  IF (length(p_password) > 16 or length(p_password) < 4) THEN raise exc_password_length; END IF;
  IF (length(p_email) < 6 or length(p_email) > 48) THEN raise exc_email_length; END IF;
  IF (REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') IS NOT NULL) THEN raise exc_non_alpnum_username; END IF;
  IF (REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') IS NOT NULL) THEN raise exc_non_alpnum_password; END IF;
  IF ((REGEXP_INSTR(p_email, '@', 1 , 1) = 0 AND REGEXP_INSTR(p_email, '@', 1 , 2) != 0) OR REGEXP_SUBSTR(p_email, '[^a-zA-Z0-9@._]+') IS NOT NULL) THEN raise exc_bad_email_format; END IF;
  IF (length(p_rootadmgrp) > 48 or length(p_rootadmgrp) < 4) THEN raise_application_error(-20007, 'The root admins group name length must be between 4 and 48 characters! It currently has: ' || length(p_rootadmgrp) || ' !'); END IF;
  IF (length(p_rootmnggrp) > 48 or length(p_rootmnggrp) < 4) THEN raise_application_error(-20007, 'The root managers group name length must be between 4 and 48 characters! It currently has: ' || length(p_rootmnggrp) || ' !'); END IF;
  IF (length(p_rootusrsgrp) > 48 or length(p_rootusrsgrp) < 4) THEN raise_application_error(-20007, 'The root normal users group name length must be between 4 and 48 characters! It currently has: ' || length(p_rootusrsgrp) || ' !'); END IF;
  
  v_rootadmgrp := REGEXP_REPLACE(p_rootadmgrp,'[^][,.?:+{}!@#$%^&()_=[:alpha:][:digit:] -]*','',1,0,'imx');
  v_rootmnggrp := REGEXP_REPLACE(p_rootmnggrp,'[^][,.?:+{}!@#$%^&()_=[:alpha:][:digit:] -]*','',1,0,'imx');
  v_rootusrsgrp := REGEXP_REPLACE(p_rootusrsgrp,'[^][,.?:+{}!@#$%^&()_=[:alpha:][:digit:] -]*','',1,0,'imx');
  
  --INSERT INTO USERACCS (userName, userEmail, userPass, userType, userState, userImage) VALUES (p_username, p_email, p_password, 3, 1, 'undefined');  
  --IF (SQL%FOUND) THEN
  --  INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (v_rootadmgrp, 'Root admins group', 0, 0);
  --  IF (SQL%FOUND) THEN
  --    INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (v_rootmnggrp, 'Root managers group', 0, 0);
  --    IF (SQL%FOUND) THEN
  --      INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (v_rootusrsgrp, 'Root normal users group', 0, 0);
  --      IF (SQL%FOUND) THEN
  --        v_result := TRUE;
  --      ELSE
  --        v_result := FALSE;
  --      END IF;      
  --    ELSE
  --      v_result := FALSE;
  --    END IF;
  --  ELSE
  --    v_result := FALSE;
  --  END IF;
  --ELSE
  --  v_result := FALSE;
  --END IF;
  
  --RETURN v_result;
  --INSERT INTO USERACCS (userName, userEmail, userPass, userType, userState, userImage) VALUES (p_username, p_email, p_password, 3, 1, 'undefined');  
  --INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (v_rootadmgrp, 'Root admins group', 0, 0);
  --INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (v_rootmnggrp, 'Root managers group', 0, 0);
  --INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (v_rootusrsgrp, 'Root normal users group', 0, 0);
  
  
  
  EXCEPTION
  WHEN exc_username_length THEN
    raise_application_error(-20001, 'The provided username length is: ' || length(p_username) || ' !. It needs to be between 4 and 16 alpha-numeric characters!');    
  WHEN exc_password_length THEN
    raise_application_error(-20002, 'The provided password length is: ' || length(p_password) || ' !. It needs to be between 4 and 16 alpha-numeric characters!');
  WHEN exc_non_alpnum_username THEN
    raise_application_error(-20003, 'Found illegal non-alpha-numeric character in the provided username: "' || REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') || '" !');
  WHEN exc_non_alpnum_password THEN
    raise_application_error(-20004, 'Found illegal non-alpha-numeric character in the provided password: "' || REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') || '" !');
  WHEN exc_email_length THEN
    raise_application_error(-20005, 'The provided email length is: ' || length(p_email) || ' !. It needs to be between 6 and 48 alpha-numeric characters!');  
  WHEN exc_bad_email_format THEN
    raise_application_error(-20006, 'Found illegal character in the provided email: "' || REGEXP_SUBSTR(p_email,'[^a-zA-Z0-9@._]+') || '" !');
  WHEN OTHERS THEN    
      RAISE;
END prc_addMainRoots;
/
SET SERVEROUTPUT ON;
DECLARE
  v_result BOOLEAN;
BEGIN
  prc_addMainRoots('&i1','&i2','&i3','&i4','&i5','&i7');
END
/
COMMIT
/
