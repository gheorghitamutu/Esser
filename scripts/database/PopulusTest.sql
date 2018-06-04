SET SERVEROUTPUT ON;
DECLARE
  v_next_userId USERACCS.USERID%TYPE;
  v_userName USERACCS.USERNAME%TYPE;
  v_userEmail USERACCS.userEmail%TYPE;
  v_userPass USERACCS.USERPASS%TYPE;
  v_ins_cmd VARCHAR2(32000);
BEGIN
  SELECT COUNT(USERID)+1 INTO v_next_userId FROM USERACCS;
  DBMS_OUTPUT.PUT_LINE(v_next_userId);
  v_ins_cmd := 'INSERT INTO USERACCS(USERID, USERNAME, USEREMAIL, USERPASS, USERTYPE, USERSTATE, USERIMAGE, USERCREATEDAT, USERUPDATEDAT) VALUES(';
  v_ins_cmd := v_ins_cmd || v_next_userId || ',';
  SELECT USERNAME INTO v_userName FROM USERACCS WHERE v_next_userId - 1 = USERID;
  SELECT USEREMAIL INTO v_userEmail FROM USERACCS WHERE v_next_userId - 1 = USERID;  
  SELECT USERPASS INTO v_userPass FROM USERACCS WHERE v_next_userId - 1 = USERID;  
  v_userName := v_userName || v_next_userId;
  v_userEmail := REPLACE(v_userEmail, '@', v_next_userId||'@');
  v_ins_cmd := v_ins_cmd || '''' ||v_userName || ''',''' || v_userEmail || ''',''' || v_userPass || ''', 8, 3, ''/assets/normal/images/users/test.png'', SYSDATE, SYSDATE)';
  DBMS_OUTPUT.PUT_LINE(v_ins_cmd);  
  EXECUTE IMMEDIATE v_ins_cmd;
END;
