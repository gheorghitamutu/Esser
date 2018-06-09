-- Creation of the database; Creation of triggers that have a complex functionality and automation (Step 5);

-- Automatic issueing of warning notifications for items that arrive at or lower than the warning treshold
CREATE OR REPLACE TRIGGER trg_auto_warn BEFORE UPDATE ON ITEMS FOR EACH ROW
DECLARE
  v_grp_id NUMBER(8,0) := 0;
  v_old_grp_id NUMBER(8,0) := 0;
  v_usr_id NUMBER(8,0) := 0;
  v_old_usr_id NUMBER(8,0) := 0;
  v_count NUMBER(8,0) := 0;
  v_text VARCHAR(2000);
  v_ntf_id NUMBER(8,0) := 0;
BEGIN
  CASE
    WHEN UPDATING('itemQuantity') THEN
      IF (:NEW.itemQuantity < 0) THEN
        v_text := 'Item quantity can be only 0 or above!';
        raise_application_error(-200998, v_text);
      END IF;
      IF (:NEW.iWarnQnty < 0) THEN
        :NEW.iWarnQnty := null;
      end if;
      IF (:NEW.iWarnQnty IS NOT NULL AND :NEW.itemQuantity <= :NEW.iWarnQnty) THEN
        v_text := 'Warning: Item ' || :NEW.itemName || ' quantity is now ' || :NEW.itemQuantity || ' !';
        UPDATE NOTIFICATIONS SET ntfDscrp = v_text WHERE nItemId = :NEW.itemId;
      END IF;
    WHEN UPDATING('iWarnQnty') THEN
      IF (:NEW.iWarnQnty < 0) THEN
        :NEW.iWarnQnty := null;
      end if;
      IF (:NEW.iWarnQnty >= :NEW.itemQuantity) THEN
        v_text := 'Warning: Item ' || :NEW.itemName || ' quantity is now ' || :NEW.itemQuantity || ' !';
        UPDATE NOTIFICATIONS SET ntfDscrp = v_text WHERE nItemId = :NEW.itemId;
        IF (SQL%NOTFOUND) THEN
          v_text := 'Warning: Item ' || :NEW.itemName || ' quantity is now ' || :NEW.itemQuantity || ' !';
          INSERT INTO NOTIFICATIONS (nItemId, ntfType, ntfDscrp) VALUES (:NEW.itemId, 2, v_text);
          INSERT INTO NOTIFICATIONS (nItemId, ntfType, ntfDscrp) VALUES (:NEW.itemId, 1, v_text);
          LOOP
            BEGIN
              SELECT igOwnerId into v_grp_id FROM (SELECT igOwnerId FROM ITEMGROUPOWNERSHIPS WHERE igOwnerId > v_grp_id ORDER BY igOwnerId ASC) WHERE ROWNUM = 1;
              EXCEPTION
              WHEN NO_DATA_FOUND THEN
                null;
            END;
            EXIT WHEN v_grp_id = v_old_grp_id;
            SELECT ntfId INTO v_ntf_id FROM NOTIFICATIONS WHERE ntfType = 2 AND nItemId = :NEW.itemId;
            INSERT INTO UGRPNTFRELATIONS (usrgnNotificationId, usrgnNotifiedGroupId) VALUES (v_ntf_id, v_grp_id);
            LOOP
              BEGIN
                SELECT userId INTO v_usr_id FROM (SELECT userId FROM GROUPRELATIONS WHERE v_usr_id < userId ORDER BY userId ASC) WHERE ROWNUM = 1;
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  null;
              END;
              EXIT WHEN v_usr_id = v_old_usr_id;
              SELECT ntfId INTO v_ntf_id FROM NOTIFICATIONS WHERE  ntfType = 1 AND nItemId = :NEW.itemId;
              INSERT  INTO USRNTFRELATIONS (usrnNotifiedAccId, usrnNotificationId) VALUES (v_usr_id, v_ntf_id);
              v_old_usr_id := v_usr_id;
            END LOOP;
            v_old_grp_id := v_grp_id;
          END LOOP;
        END IF;
      END IF;
      IF (:NEW.iWarnQnty < :NEW.itemQuantity) THEN
        LOOP
          SELECT ntfId, COUNT(ntfId) into v_ntf_id, v_count FROM (SELECT ntfID FROM NOTIFICATIONS ORDER BY ntfId ASC) WHERE ROWNUM = 1 GROUP BY ntfId;
          EXIT WHEN v_count = 0;
          DELETE FROM USRNTFRELATIONS WHERE usrnNotificationId = v_ntf_id;
          DELETE FRoM UGRPNTFRELATIONS WHERE usrgnNotificationId = v_ntf_id;
        END LOOP;
        DELETE FROM NOTIFICATIONS WHERE nItemId = :NEW.itemId;
      end if;
    WHEN UPDATING('itemName') THEN
      v_text := 'Warning: Item ' || :NEW.itemName || ' quantity is now ' || :NEW.itemQuantity || ' !';
      UPDATE NOTIFICATIONS SET ntfDscrp = v_text WHERE nItemId = :NEW.itemId;
  END CASE;
END trg_auto_warn;
/

-- Automatic calculation of the nr of members in a group and nr of managers in a group -- Erroneous for now --
--CREATE OR REPLACE TYPE nrOfMbsMngs_line AS OBJECT (
--  uGroupId     NUMBER,
--  nrOfMembers  NUMBER,
--  nrOfManagers NUMBER
--);
--/
--CREATE OR REPLACE TYPE BODY nrOfMbsMngs_line AS
--
--  MEMBER PROCEDURE incrNrOfMembers AS
--    BEGIN
--      SELF.nrOfManagers := SELF.nrOfManagers + 1;
--    END;
--
--  MEMBER PROCEDURE dcrNrOfMembers AS
--    BEGIN
--      SELF.nrOfManagers := SELF.nrOfManagers - 1;
--    END;
--
--  MEMBER PROCEDURE incrNrOfManagers AS
--    BEGIN
--      SELF.nrOfManagers := SELF.nrOfManagers + 1;
--    END;
--
--  MEMBER PROCEDURE dcrNrOfManagers AS
--    BEGIN
--      SELF.nrOfManagers := SELF.nrOfManagers - 1;
--    END;
--
--  MEMBER FUNCTION getNrOfMembers(p_uGroupId NUMBER) RETURN NUMBER IS
--    v_number NUMBER := 0;
--    BEGIN
--      SELECT NROFMEMBERS INTO v_number FROM USERGROUPS WHERE UGROUPID = p_uGroupId;
--      RETURN v_number;
--    END getNrOfMembers;
--
--  MEMBER FUNCTION getNrOfManagers(p_uGroupId NUMBER) RETURN NUMBER IS
--    v_number NUMBER := 0;
--    BEGIN
--      SELECT NROFMANAGERS INTO v_number FROM USERGROUPS WHERE UGROUPID = p_uGroupId;
--      RETURN v_number;
--    END getNrOfManagers;
--
--  CONSTRUCTOR FUNCTION nrOfMbsMngs_line(p_uGroupId NUMBER, p_nrOfMembers  NUMBER, p_nrOfManagers NUMBER) RETURN SELF AS RESULT AS
--    BEGIN
--      SELF.uGroupId := p_uGroupId;
--      SELF.nrOfMembers := p_nrOfMembers;
--      SELF.nrOfManagers := p_nrOfManagers;
--      RETURN;
--    END;
--
--  CONSTRUCTOR FUNCTION nrOfMbsMngs_line(p_uGroupId NUMBER) RETURN SELF AS RESULT AS
--    BEGIN
--      SELF.uGroupId := p_uGroupId;
--      SELF.nrOfMembers := getNrOfMembers(p_uGroupId);
--      SELF.nrOfManagers := getNrOfManagers(p_uGroupId);
--    END;
--
--  CONSTRUCTOR FUNCTION nrOfMbsMngs_line RETURN SELF AS RESULT AS
--    BEGIN
--      SELF.uGroupId := 0;
--      SELF.nrOfMembers := 0;
--      SELF.nrOfManagers := 0;
--    END;
--
--END;
--/
--CREATE OR REPLACE TYPE tbl_of_nrOfMbsMngs AS TABLE OF nrOfMbsMngs_line;
--/
--CREATE OR REPLACE TRIGGER trg_auto_nrOf FOR INSERT OR UPDATE OR DELETE ON GROUPRELATIONS
--COMPOUND TRIGGER
--  v_i NUMBER := 0;
--  v_count NUMBER(8,0);
--  v_error VARCHAR2(2000);
--  v_tbl_of_nrOfMbsMngs_line tbl_of_nrOfMbsMngs := tbl_of_nrOfMbsMngs();
--  AFTER EACH ROW IS
--    v_nrOfMbsMngs_line nrOfMbsMngs_line;
--  BEGIN
--  CASE
--    WHEN INSERTING THEN
--    IF (:NEW.canMngMbs NOT IN (0,1)) THEN
--      v_error := 'You can only disable(0) or enable(1) a members ability to manage members from his group!';
--      raise_application_error(-20999, v_error);
--    ELSIF (:NEW.canUpdItm NOT IN (0,1)) THEN
--      v_error := 'You can only disable(0) or enable(1) a members ability to manage items in his group!';
--      raise_application_error(-20999, v_error);
--    END IF;
--    IF (:NEW.canMngMbs = 1) THEN
--      IF (v_tbl_of_nrOfMbsMngs_line.COUNT < :NEW.UGROUPID) THEN
--        v_tbl_of_nrOfMbsMngs_line.EXTEND(:NEW.UGROUPID - v_tbl_of_nrOfMbsMngs_line.COUNT);
--        v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--        v_nrOfMbsMngs_line.incrNrOfManagers();
--        v_nrOfMbsMngs_line.incrNrOfMembers();
--        v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--      ELSIF (v_tbl_of_nrOfMbsMngs_line.COUNT >= :NEW.UGROUPID) THEN
--        IF (v_tbl_of_nrOfMbsMngs_line.EXISTS(:NEW.UGROUPID)) THEN
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfMembers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfMembers + 1;
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers + 1;
--        ELSE
--          v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--          v_nrOfMbsMngs_line.incrNrOfManagers();
--          v_nrOfMbsMngs_line.incrNrOfMembers();
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--        END IF;
--      END IF;
--    END IF;
--    WHEN UPDATING('CANUPDITM') THEN
--    IF (:NEW.canUpdItm NOT IN (0,1)) THEN
--      v_error := 'You can only disable(0) or enable(1) a members ability to manage items in his group!';
--      raise_application_error(-20999, v_error);
--    END IF;
--    WHEN UPDATING('CANMNGMBS') THEN
--    v_error := 'You can only disable(0) or enable(1) a members ability to manage members from his group!';
--    IF (:NEW.canMngMbs NOT IN (0,1)) THEN
--      raise_application_error(-20999, v_error);
--    END IF;
--    IF (:NEW.canMngMbs = 0 AND :OLD.canMngMbs = 1) THEN
--      IF (v_tbl_of_nrOfMbsMngs_line.COUNT < :NEW.UGROUPID) THEN
--        v_tbl_of_nrOfMbsMngs_line.EXTEND(:NEW.UGROUPID - v_tbl_of_nrOfMbsMngs_line.COUNT);
--        v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--        v_nrOfMbsMngs_line.dcrNrOfManagers();
--        v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--      ELSIF (v_tbl_of_nrOfMbsMngs_line.COUNT >= :NEW.UGROUPID) THEN
--        IF (v_tbl_of_nrOfMbsMngs_line.EXISTS(:NEW.UGROUPID)) THEN
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers - 1;
--        ELSE
--          v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--          v_nrOfMbsMngs_line.dcrNrOfManagers();
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--        END IF;
--      END IF;
--    END IF;
--    IF (:NEW.canMngMbs = 1 AND :OLD.canMngMbs = 0) THEN
--      IF (v_tbl_of_nrOfMbsMngs_line.COUNT < :NEW.UGROUPID) THEN
--        v_tbl_of_nrOfMbsMngs_line.EXTEND(:NEW.UGROUPID - v_tbl_of_nrOfMbsMngs_line.COUNT);
--        v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--        v_nrOfMbsMngs_line.incrNrOfMembers();
--        v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--      ELSIF (v_tbl_of_nrOfMbsMngs_line.COUNT >= :NEW.UGROUPID) THEN
--        IF (v_tbl_of_nrOfMbsMngs_line.EXISTS(:NEW.UGROUPID)) THEN
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers + 1;
--        ELSE
--          v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--          v_nrOfMbsMngs_line.incrNrOfMembers();
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--        END IF;
--      END IF;
--    END IF;
--    WHEN DELETING THEN
--    IF (:OLD.canMngMbs = 1) THEN
--      IF (v_tbl_of_nrOfMbsMngs_line.COUNT < :NEW.UGROUPID) THEN
--        v_tbl_of_nrOfMbsMngs_line.EXTEND(:NEW.UGROUPID - v_tbl_of_nrOfMbsMngs_line.COUNT);
--        v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--        v_nrOfMbsMngs_line.dcrNrOfManagers();
--        v_nrOfMbsMngs_line.dcrNrOfMembers();
--        v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--      ELSIF (v_tbl_of_nrOfMbsMngs_line.COUNT >= :NEW.UGROUPID) THEN
--        IF (v_tbl_of_nrOfMbsMngs_line.EXISTS(:NEW.UGROUPID)) THEN
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfManagers - 1;
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfMembers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfMembers - 1;
--        ELSE
--          v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--          v_nrOfMbsMngs_line.dcrNrOfManagers();
--          v_nrOfMbsMngs_line.dcrNrOfMembers();
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--        END IF;
--      END IF;
--    ELSE
--      IF (v_tbl_of_nrOfMbsMngs_line.COUNT < :NEW.UGROUPID) THEN
--        v_tbl_of_nrOfMbsMngs_line.EXTEND(:NEW.UGROUPID - v_tbl_of_nrOfMbsMngs_line.COUNT);
--        v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--        v_nrOfMbsMngs_line.dcrNrOfMembers();
--        v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--      ELSIF (v_tbl_of_nrOfMbsMngs_line.COUNT >= :NEW.UGROUPID) THEN
--        IF (v_tbl_of_nrOfMbsMngs_line.EXISTS(:NEW.UGROUPID)) THEN
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfMembers := v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID).nrOfMembers - 1;
--        ELSE
--          v_nrOfMbsMngs_line := nrOfMbsMngs_line(:NEW.UGROUPID);
--          v_nrOfMbsMngs_line.dcrNrOfMembers();
--          v_tbl_of_nrOfMbsMngs_line(:NEW.UGROUPID) := v_nrOfMbsMngs_line;
--        END IF;
--      END IF;
--    END IF;
--  END CASE;
--  END AFTER EACH ROW;
--
--  AFTER STATEMENT IS
--  BEGIN
--      FOR v_i IN v_tbl_of_nrOfMbsMngs_line.FIRST..v_tbl_of_nrOfMbsMngs_line.LAST LOOP
--        IF (v_tbl_of_nrOfMbsMngs_line.EXISTS(v_i)) THEN
--          UPDATE USERGROUPS
--          SET NROFMANAGERS = v_tbl_of_nrOfMbsMngs_line(v_i).nrOfManagers,
--              NROFMEMBERS = v_tbl_of_nrOfMbsMngs_line(v_i).nrOfMembers
--          WHERE v_tbl_of_nrOfMbsMngs_line(v_i).uGroupId = UGROUPID and v_i = UGROUPID;
--        END IF;
--      END LOOP;
--  END AFTER STATEMENT;
--END trg_auto_nrOf;
--/
