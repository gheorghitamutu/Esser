-- Creation of the database; Creation of DB procedures, functions and the rest of the lot (STEP 6);

CREATE OR REPLACE FUNCTION fct_addNewRootAdm(p_username VARCHAR2, p_password VARCHAR2, p_email VARCHAR2)
RETURN BOOLEAN
AS
  v_usernamae VARCHAR2(16);
  v_password VARCHAR2(16);
  v_email VARCHAR2(48);
  v_userImage VARCHAR2(256);
  v_sql_cmd VARCHAR2(2000);
  v_result BOOLEAN;
  exc_username_length exception;
  PRAGMA EXCEPTION_INIT(exc_username_length, -20001);
  exc_password_length exception;
  PRAGMA EXCEPTION_INIT(exc_password_length, -20002);
  exc_non_alpnum_username exception;
  PRAGMA EXCEPTION_INIT(exc_non_alpnum_username, -20003);
  exc_non_alpnum_password exception;
  PRAGMA EXCEPTION_INIT(exc_non_alpnum_password, -20004);
  exc_email_length exception;
  PRAGMA EXCEPTION_INIT(exc_email_length, -20005);
  exc_bad_email_format exception;
  PRAGMA EXCEPTION_INIT(exc_bad_email_format, -20006);
BEGIN  
  IF (length(p_username) > 16 or length(p_username) < 4) THEN
    raise exc_username_length;
  END IF;
  IF (length(p_password) > 16 or length(p_password) < 4) THEN
    raise exc_password_length;
  END IF;
  IF (length(p_email) < 6 or length(p_email) > 48) THEN
    raise exc_email_length;
  END IF;
  IF (REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') IS NOT NULL) THEN
    raise exc_non_alpnum_username;
  END IF;
  IF (REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') IS NOT NULL) THEN
    raise exc_non_alpnum_password;
  END IF;
  IF ((REGEXP_INSTR(p_email, '@', 1 , 1) = 0 AND REGEXP_INSTR(p_email, '@', 1 , 2) != 0) OR REGEXP_SUBSTR(p_email, '[^a-zA-Z0-9@._]+') IS NOT NULL) THEN
    raise exc_bad_email_format;
  END IF;
  
  INSERT INTO USERACCS (userName, userEmail, userPass, userType, userState, userImage) VALUES (p_username, p_email, p_password, 3, 1, 'undefined');
  
  IF (SQL%FOUND) THEN
    v_result := TRUE;
  ELSE
    v_result := FALSE;
  END IF;
  
  return v_result;
  
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
      raise;
END fct_addNewRootAdm;
/
CREATE OR REPLACE FUNCTION fct_addRootGrps(p_rootgrpname VARCHAR2, p_rootgrpdescrp VARCHAR2) RETURN BOOLEAN
AS
  v_result BOOLEAN;
  exc_grpname_length EXCEPTION;
  exc_grpname_bad_format EXCEPTION;
  exc_grpdscr_length EXCEPTION;
  exc_grpdscr_bad_format EXCEPTION;
  PRAGMA EXCEPTION_INIT(exc_grpname_length, -20007);
  PRAGMA EXCEPTION_INIT(exc_grpname_bad_format, -20008);
  PRAGMA EXCEPTION_INIT(exc_grpdscr_length, -20009);
  PRAGMA EXCEPTION_INIT(exc_grpdscr_bad_format, -20010);
BEGIN
  IF (length(p_rootgrpname) > 48 or length(p_rootgrpname) < 4) THEN
    raise exc_grpname_length;
  END IF;
  IF (REGEXP_SUBSTR(p_rootgrpname,'[^a-zA-Z0-9\-_ ]+') IS NOT NULL) THEN
    raise exc_grpname_bad_format;
  END IF;
  IF (length(p_rootgrpdescrp) > 2000 or length(p_rootgrpdescrp) < 4) THEN
    raise exc_grpdscr_length;
  END IF;
  IF (REGEXP_SUBSTR(p_rootgrpdescrp, '[^a-zA-Z0-9+\-_.,(){}\[\]=:\n\t\r<>!?*#$%&@^ ]+') IS NOT NULL) THEN
    raise exc_grpdscr_bad_format;
  END IF;

  INSERT INTO USERGROUPS (uGroupName, uGroupDescription, nrOfMembers, nrOfManagers) VALUES (p_rootgrpname, p_rootgrpdescrp, 0, 0);

  IF (SQL%FOUND) THEN
    v_result := true;
  ELSE
    v_result := false;
  END IF;

  return v_result;

END fct_addRootGrps;
/
DECLARE
  v_result BOOLEAN;
BEGIN
  v_result := fct_addNewRootAdm('&i1', '&i2','&i3');
  IF (v_result = true) THEN COMMIT;
  ELSE ROLLBACK;
  END IF;
END;
/
DECLARE
  v_result BOOLEAN;
BEGIN
  v_result := fct_addRootGrps('&i4', 'Root admins group');
  IF (v_result = true) THEN COMMIT;
  ELSE ROLLBACK ;
  END IF;
END;
/
DECLARE
  v_result BOOLEAN;
BEGIN
  v_result := fct_addRootGrps('&i5', 'Root managers group');
  IF (v_result = true) THEN COMMIT;
  ELSE ROLLBACK;
  END IF;
END;
/
DECLARE
  v_result BOOLEAN;
BEGIN
  v_result := fct_addRootGrps('&i6', 'Root normal users group');
  IF (v_result = true) THEN COMMIT;
  ELSE ROLLBACK;
  END IF;
END;
/
COMMIT;
/
