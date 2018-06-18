CREATE OR REPLACE TRIGGER trg_add_to_nrm_usr AFTER INSERT ON USERACCS FOR EACH ROW
  DECLARE
    v_mbm_count NUMBER(8,0);
    v_mng_count NUMBER(8,0);
  BEGIN
    IF (:NEW.USERID = 1) THEN
      NULL;
    ELSE
      SELECT COUNT(*) INTO v_mbm_count FROM GROUPRELATIONS WHERE UGROUPID = 3;
      SELECT COUNT(*) INTO v_mng_count FROM GROUPRELATIONS WHERE UGROUPID = 3 AND CANMNGMBS = 1;
      INSERT INTO GROUPRELATIONS (USERID, UGROUPID, CANUPDITM, CANMNGMBS) VALUES (:NEW.USERID, 3, 0, 0);
      UPDATE USERGROUPS SET NROFMEMBERS = v_mbm_count, NROFMANAGERS = v_mng_count WHERE UGROUPID = 3;
    END IF;
  END;
/
