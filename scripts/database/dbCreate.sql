-- Creation of the database; Need to remove the DROP TABLE STATEMENTS before live production
BEGIN
  EXECUTE IMMEDIATE('DROP PROCEDURE prc_drop_all_obj');
  EXCEPTION
  WHEN OTHERS THEN 
    IF SQLCODE = -4043 THEN
      null;
    ELSE
      RAISE;
    END IF;
END;
/
SET SERVEROUTPUT ON;
CREATE OR REPLACE PROCEDURE prc_drop_all_obj(p_number NUMBER, p_type IN VARCHAR2)
AS
  v_i NUMBER := p_number;
  v_type VARCHAR2(50) := p_type;
  v_sql_drop VARCHAR2(1000);
  TYPE obj_name_arr IS VARRAY(15) OF VARCHAR2(100);
  v_tbl_name_arr obj_name_arr := obj_name_arr('USERACCS', 'ITEMGROUPS', 'ITEMS', 'USERGROUPS', 'GROUPRELATIONS', 'ITEMOWNERSHIPS', 'ITEMGROUPOWNERSHIPS', 'USERGROUPLOGS', 'ITEMGROUPLOGS', 'USERLOGS', 'ITEMLOGS', 'AUTOMATEDREPORTS', 'NOTIFICATIONS', 'UGRPNTFRELATIONS', 'USRNTFRELATIONS');
  v_seq_name_arr obj_name_arr := obj_name_arr('INCR_USERACCS', 'INCR_ITMGRPS', 'INCR_ITMS', 'INCR_USERGRPS', 'INCR_GRPRELS', 'INCR_ITMOWNS', 'INCR_ITMGRPOWNS', 'INCR_USRGRPLOGS', 'INCR_ITMGRPLOGS', 'INCR_USRLOGS', 'INCR_ITMLOGS', 'INCR_AUTOREP', 'INCR_NOTIFS', 'INCR_UGRPNTFSREL', 'INCR_USRNTFREL');
  v_trg_name_arr obj_name_arr := obj_name_arr('TRG_INCR_USERACCS', 'TRG_INCR_ITMGRPS', 'TRG_INCR_ITMS', 'TRG_INCR_USERGRPS', 'TRG_INCR_GRPRELS', 'TRG_INCR_ITMOWNS', 'TRG_INCR_ITMGRPOWNS', 'TRG_INCR_USRGRPLOGS', 'TRG_INCR_ITMGRPLOGS', 'TRG_INCR_USRLOGS', 'TRG_INCR_ITMLOGS', 'TRG_INCR_AUTOREP', 'TRG_INCR_NOTIFS', 'TRG_INCR_UGRPNTFSREL', 'TRG_INCR_USRNTFREL');
BEGIN
  IF (v_i < 1 or v_i > 16) THEN
    raise_application_error(-20998, 'Bad number ("' || v_i || '")  selected! Only numbers allowed are from 1 to 15!');    
  END IF;
  IF (lower(v_type) LIKE 'all_tables')  THEN
    v_type := 'all_tables';
    WHILE (v_i <= v_tbl_name_arr.COUNT AND v_type LIKE 'all_tables') LOOP      
      IF (v_tbl_name_arr.EXISTS(v_i)) THEN
        v_sql_drop := 'DROP TABLE ' || v_tbl_name_arr(v_i) || ' CASCADE CONSTRAINTS PURGE';
        EXECUTE IMMEDIATE(v_sql_drop);
        DBMS_OUTPUT.PUT_LINE('Successfully droped table ' || v_tbl_name_arr(v_i) || '!');
        v_i := v_i + 1;      
      END IF;
    END LOOP;
    DBMS_OUTPUT.PUT_LINE('All table names have been cleaned');
  ELSIF (lower(v_type) LIKE 'all_sequences') THEN
    v_type := 'all_sequences';
    WHILE (v_i <= v_seq_name_arr.COUNT AND v_type LIKE 'all_sequences') LOOP
      IF (v_seq_name_arr.EXISTS(v_i)) THEN
        v_sql_drop := 'DROP SEQUENCE ' || UPPER(v_seq_name_arr(v_i) || '');
        EXECUTE IMMEDIATE(v_sql_drop);
        DBMS_OUTPUT.PUT_LINE('Successfully droped sequence ' || v_seq_name_arr(v_i) || '!');
        v_i := v_i + 1;
      END IF;
    END LOOP;
  ELSE
    raise_application_error(-20999, 'Bad type ("' || lower(v_type) || '")  selected! Only "all_tables" or "all_sequences" is allowed!');
  END IF;
  DBMS_OUTPUT.PUT_LINE('Procedure finished!');
  EXCEPTION
  WHEN OTHERS THEN
    IF (SQLCODE = -942 OR SQLCODE = -2289 OR SQLCODE = -6512) THEN
      prc_drop_all_obj(v_i + 1, v_type);
    ELSE
      raise;
    END IF;
END prc_drop_all_obj;
/
SET SERVEROUTPUT ON;
BEGIN
  prc_drop_all_obj(1, 'all_tables');
  prc_drop_all_obj(1, 'all_sequences');
END;
/

CREATE TABLE USERACCS(
  userId NUMBER(*,0) NOT NULL,
  userName VARCHAR2(16) NOT NULL,
  userEmail VARCHAR2(48) NOT NULL,
  userPass VARCHAR2(16) NOT NULL,
  userType  NUMBER(1) DEFAULT 0,
  userState NUMBER(1) DEFAULT 1,
  userImage varchar2(256) DEFAULT NULL,
  userCreatedAt DATE NOT NULL,
  userUpdatedAt DATE NOT NULL,  
  CONSTRAINT pk_userId PRIMARY KEY(userId),
  CONSTRAINT unq_useremail UNIQUE(userEmail),
  CONSTRAINT not_null_usertype CHECK (userType is not null),
  CONSTRAINT not_null_userstate CHECK (userState is not null)
)
/
CREATE TABLE USERLOGS(
  uLogId NUMBER(*,0) NOT NULL,
  uLogDescription VARCHAR2(2000) DEfAULT 'Log about a user',
  uLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  uLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_uLogId PRIMARY KEY (uLogId),
  CONSTRAINT not_null_udscr CHECK (uLogDescription IS NOT NULL),
  CONSTRAINT not_null_uip CHECK (uLogSourceIP IS NOT NULL)
)
/
CREATE TABLE USERGROUPS(
  uGroupId NUMBER(*,0) NOT NULL,
  uGroupName VARCHAR2(48) DEFAULT 'Group of users',
  uGroupDescription VARCHAR2(2000) DEFAULT 'Description about group of users',
  nrOfMembers NUMBER(*,0) DEFAULT 0,
  nrOfManagers NUMBER(*,0) DEFAULT 0,
  uGroupCreatedAt DATE NOT NULL,
  uGroupUpdatedAt DATE NOT NULL,
  CONSTRAINT pk_uGroupId PRIMARY KEY(uGroupId),
  CONSTRAINT not_null_nrofmbs CHECK (nrOfMembers is not null),
  CONSTRAINT not_null_nrofmngs CHECK (nrOfManagers is not null),
  CONSTRAINT not_null_ugrpname CHECK (uGroupName is not null),
  CONSTRAINT uniq_ugrpname UNIQUE (uGroupName),
  CONSTRAINT not_null_ugrpdescrp CHECK (uGroupDescription is not null)
)
/
CREATE TABLE USERGROUPLOGS(
  ugLogId NUMBER(*,0) NOT NULL,
  ugLogDescription VARCHAR2(2000) DEfAULT 'Log about an user group',
  ugLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  ugLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_ugLogId PRIMARY KEY (ugLogId),
  CONSTRAINT not_null_ugdscr CHECK (ugLogDescription IS NOT NULL),
  CONSTRAINT not_null_ugip CHECK (ugLogSourceIP IS NOT NULL)
)
/
CREATE TABLE GROUPRELATIONS(
  relationId NUMBER(*,0) NOT NULL,
  userId NUMBER(*,0) NOT NULL,
  uGroupId NUMBER(*,0) NOT NULL,
  canUpdItm NUMBER(1) DEFAULT 1,
  canMngMbs NUMBER(1) DEFAULT 0,
  grpRelCreatedAt DATE NOT NULL,
  grpRelUpdatedAt DATE NOT NULL,
  CONSTRAINT pk_relationId PRIMARY KEY(relationId),
  CONSTRAINT fk_rUserId FOREIGN KEY(userId) REFERENCES USERACCS(userId),
  CONSTRAINT fk_rGroupId FOREIGN KEY(uGroupId) REFERENCES USERGROUPS(uGroupId),
  CONSTRAINT not_null_canupditm CHECK (canUpdItm is not null),
  CONSTRAINT not_null_canmngmbs CHECK (canMngMbs is not null)
)
/
CREATE TABLE ITEMGROUPS(
  iGroupId NUMBER(*,0) NOT NULL,
  iGroupName VARCHAR2(48) DEFAULT 'Group of items',
  iGroupDescription VARCHAR2(2000) DEFAULT 'Description about group of items',
  iGroupCreatedAt DATE NOT NULL,
  iGroupUpdatedAt DATE NOT NULL,
  CONSTRAINT pk_iGroupId PRIMARY KEY(iGroupId),
  CONSTRAINT not_null_igrpname CHECK (iGroupName is not null),
  CONSTRAINT unq_igrpname UNIQUE (iGroupName),
  CONSTRAINT not_null_igrpdescrp CHECK (iGroupDescription is not null)
)
/
CREATE TABLE ITEMGROUPLOGS(
  igLogId NUMBER(*,0) NOT NULL,
  igLogDescription VARCHAR2(2000) DEfAULT 'Log about an item group',
  igLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  igLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_igLogId PRIMARY KEY (igLogId),
  CONSTRAINT not_null_igdscr CHECK (igLogDescription IS NOT NULL),
  CONSTRAINT not_null_igip CHECK (igLogSourceIP IS NOT NULL)
)
/
CREATE TABLE ITEMGROUPOWNERSHIPS(
  igOwnershipId NUMBER(*,0) NOT NULL,
  igOwnerId NUMBER(*,0) NOT NULL,
  igId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_igownId PRIMARY KEY (igOwnershipId),
  CONSTRAINT fk_igownerId FOREIGN KEY (igOwnerId) REFERENCES USERGROUPS(uGroupId),
  CONSTRAINT fk_igownedId FOREIGN KEY (igId) REFERENCES ITEMGROUPS(iGroupId)
)
/
CREATE TABLE ITEMS(
  itemId NUMBER(*,0) NOT NULL,
  itemName VARCHAR2(48) DEFAULT 'An item',
  itemDescription VARCHAR2(2000) DEFAULT 'Description about an item',
  itemQuantity NUMBER(*,0) DEFAULT 0,
  iGroupId NUMBER(*,0) NOT NULL,
  iWarnQnty NUMBER(*,0) DEFAULT NULL,
  itemImage VARCHAR2(256) DEFAULT NULL,
  itemCreatedAt DATE NOT NULL,
  itemUpdatedAt DATE NOT NULL,
  CONSTRAINT pk_itemId PRIMARY KEY(itemId),
  CONSTRAINT fk_igroupId FOREIGN KEY (iGroupId) REFERENCES ITEMGROUPS(iGroupId),
  CONSTRAINT not_null_itemqnty CHECK (itemQuantity is not null),
  CONSTRAINT not_null_itemname CHECK (itemName is not null),
  CONSTRAINT not_null_itemdescrp CHECK (itemDescription is not null)
)
/
CREATE TABLE ITEMLOGS(
  iLogId NUMBER(*,0) NOT NULL,
  iLogDescription VARCHAR2(2000) DEfAULT 'Log about an item',
  iLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  iLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_iLogId PRIMARY KEY (iLogId),
  CONSTRAINT not_null_ildscr CHECK (iLogDescription IS NOT NULL),
  CONSTRAINT not_null_ilip CHECK (iLogSourceIP IS NOT NULL)
)
/
CREATE TABLE AUTOMATEDREPORTS(
  reportId NUMBER(*,0) NOT NULL,
  reportPath VARCHAR2(128) DEfAULT './public/assets/reports/auto/${reportType}/${formatDateTime(rCreatedAt)}.${reportFormat}',
  reportType VARCHAR2(10) DEFAULT 'daily',
  reportFormat VARCHAR2(4) DEFAULT 'xml',
  rCreatedAt DATE NOT NULL,
  CONSTRAINT pk_reportId PRIMARY KEY (reportId),
  CONSTRAINT not_null_rPath CHECK (reportPath IS NOT NULL),
  CONSTRAINT not_null_rType CHECK (reportType IS NOT NULL),
  CONSTRAINT not_null_rFormat CHECK (reportFormat IS NOT NULL)
)
/
CREATE TABLE NOTIFICATIONS(
  ntfId NUMBER(*,0) NOT NULL,
  nItemId NUMBER(*,0) NOT NULL,
  ntfType NUMBER(1) DEFAULT '0',
  ntfDscrp VARCHAR2(2000) DEFAULT 'Notification context, a warning, something',
  ntfCreatedAt DATE NOT NULL,
  CONSTRAINT pk_ntfId PRIMARY KEY (ntfId),
  CONSTRAINT fk_nItemId  FOREIGN KEY (nItemId) REFERENCES ITEMS (itemId),
  CONSTRAINT not_null_ntfType CHECK (ntfType IS NOT NULL),
  CONSTRAINT not_null_ntfDscrp CHECK (ntfDscrp IS NOT NULL)
)
/
CREATE TABLE UGRPNTFRELATIONS(
  usrgnRelationId NUMBER(*,0) NOT NULL,
  usrgnNotificationId NUMBER(*,0) NOT NULL,
  usrgnNotifiedGroupId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_ugNRelId PRIMARY KEY (usrgnRelationId),
  CONSTRAINT fk_usrgnNotifiedGroupId FOREIGN KEY (usrgnNotifiedGroupId) REFERENCES USERGROUPS (uGroupId),
  CONSTRAINT fk_usrgnNotificationId  FOREIGN KEY (usrgnNotificationId) REFERENCES NOTIFICATIONS (ntfId)
)
/
CREATE TABLE USRNTFRELATIONS(
  usrnRelationId NUMBER(*,0) NOT NULL,
  usrnNotifiedAccId NUMBER(*,0) NOT NULL,
  usrnNotificationId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_usrnRelationId PRIMARY KEY (usrnRelationId),
  CONSTRAINT fk_usrnNotifiedAccId FOREIGN KEY (usrnNotifiedAccId) REFERENCES USERACCS (userId),
  CONSTRAINT fk_usrnNotificationId FOREIGN KEY (usrnNotificationId) REFERENCES NOTIFICATIONS (ntfId)
)
/
--CREATE SEQUENCE incr_itmowns START WITH 1 INCREMENT BY 1;
--/
--CREATE TABLE ITEMOWNERSHIPS(
--  iOwnershipId NUMBER(*,0) NOT NULL,
--  iOwnerId NUMBER(*,0) NOT NULL,
--  iId NUMBER(*,0) NOT NULL,
--  CONSTRAINT pk_iownId PRIMARY KEY (iOwnershipId),
--  CONSTRAINT fk_ownerId FOREIGN KEY (iOwnerId) REFERENCES USERACCS(userId),
--  CONSTRAINT fk_owneditmId FOREIGN KEY (iId) REFERENCES ITEMS(itemId)
--)
--/
--CREATE OR REPLACE TRIGGER trg_incr_itmowns BEFORE INSERT ON ITEMOWNERSHIPS FOR EACH ROW
--BEGIN
--  :NEW.iOwnershipId := INCR_ITMOWNS.NEXTVAL;
--END trg_incr_itmowns;
--/


CREATE SEQUENCE incr_useraccs start with 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_useraccs BEFORE INSERT ON USERACCS FOR EACH ROW
BEGIN
  :NEW.USERID := incr_useraccs.NEXTVAL;
  :NEW.USERCREATEDAT := SYSDATE;  
  :NEW.USERUPDATEDAT := SYSDATE;
END trg_incr_useraccs;
/
CREATE OR REPLACE TRIGGER trg_upd_useraccs BEFORE UPDATE ON USERACCS FOR EACH ROW
BEGIN
  :NEW.USERUPDATEDAT := SYSDATE;
END trg_upd_useraccs;
/


CREATE SEQUENCE incr_itmgrps START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmgrps BEFORE INSERT ON ITEMGROUPS FOR EACH ROW
BEGIN
  :NEW.iGroupId := INCR_ITMGRPS.NEXTVAL;
  :NEW.iGroupCreatedAt := SYSDATE;
  :NEW.iGroupUpdatedAt := SYSDATE;
END trg_incr_itmgrps;
/
CREATE OR REPLACE TRIGGER trg_upd_itmgrps BEFORE UPDATE ON ITEMGROUPS FOR EACH ROW
BEGIN 
  :NEW.iGroupUpdatedAt := SYSDATE;
END trg_upd_itmgrps;
/
CREATE OR REPLACE TRIGGER trg_auto_own_igrp AFTER INSERT ON ITEMGROUPS FOR EACH ROW
BEGIN
  INSERT INTO ITEMGROUPOWNERSHIPS (igOwnerId, igId) VALUES (1 , :NEW.iGroupId);
  INSERT INTO ITEMGROUPOWNERSHIPS (igOwnerId, igId) VALUES (2 , :NEW.iGroupId);
END trg_auto_own_igrp;
/


CREATE SEQUENCE incr_itms START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itms BEFORE INSERT ON ITEMS FOR EACH ROW
BEGIN
  :NEW.itemId := INCR_ITMS.NEXTVAL;
  :NEW.itemCreatedAt := SYSDATE;
  :NEW.itemUpdatedAt := SYSDATE;
END trg_incr_itms;
/
CREATE OR REPLACE TRIGGER trg_upd_itms BEFORE UPDATE ON ITEMS FOR EACH ROW
BEGIN
  :NEW.itemUpdatedAt := SYSDATE;
END trg_upd_itms;
/
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
CREATE SEQUENCE incr_usergrps START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_usergrps BEFORE INSERT ON USERGROUPS FOR EACH ROW
BEGIN
  :NEW.uGroupId := incr_usergrps.NEXTVAL;
  :NEW.uGroupCreatedAt := SYSDATE;
  :NEW.uGroupUpdatedAt := SYSDATE;
END trg_incr_usergrps;
/
CREATE OR REPLACE TRIGGER trg_upd_usergrps BEFORE UPDATE ON USERGROUPS FOR EACH ROW
BEGIN
  :NEW.uGroupUpdatedAt := SYSDATE;
END trg_upd_usergrps;
/
CREATE OR REPLACE TRIGGER trg_auto_root_add AFTER INSERT ON USERGROUPS FOR EACH ROW
DECLARE
  CURSOR list_root_admins IS SELECT * FROM USERACCS WHERE userType = 3;
  CURSOR list_root_managers IS SELECT * FROM USERACCS WHERE userType = 2;
  v_list_root_admins list_root_admins%ROWTYPE;
  v_list_root_managers list_root_managers%ROWTYPE;
BEGIN
  IF (:NEW.uGroupId = 1) THEN
    OPEN list_root_admins;
    LOOP
      FETCH list_root_admins into v_list_root_admins;
      EXIT WHEN list_root_admins%NOTFOUND;
      INSERT INTO GROUPRELATIONS (USERID, UGROUPID, CANUPDITM, CANMNGMBS) VALUES (v_list_root_admins.userId, :NEW.uGroupId, 1, 1);
    END LOOP;
  ELSE
    LOOP
      FETCH list_root_admins into v_list_root_admins;
      EXIT WHEN list_root_admins%NOTFOUND;
      INSERT INTO GROUPRELATIONS (USERID, UGROUPID, CANUPDITM, CANMNGMBS) VALUES (v_list_root_admins.userId, :NEW.uGroupId, 1, 1);
    END LOOP;
    LOOP
      FETCH list_root_managers into v_list_root_managers;
      EXIT WHEN list_root_managers%NOTFOUND;
      INSERT INTO GROUPRELATIONS (USERID, UGROUPID, CANUPDITM, CANMNGMBS) VALUES (v_list_root_managers.userId, :NEW.uGroupId, 1, 1);
    END LOOP;
  END IF;
END;
/


CREATE SEQUENCE incr_grprels START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_grprels BEFORE INSERT ON GROUPRELATIONS FOR EACH ROW
BEGIN
  :NEW.relationId := incr_grprels.NEXTVAL;
  :NEW.grpRelCreatedAt := SYSDATE;
  :NEW.grpRelUpdatedAt := SYSDATE;
END trg_incr_grprels;
/
CREATE OR REPLACE TRIGGER trg_upd_grprels BEFORE UPDATE ON GROUPRELATIONS FOR EACH ROW
BEGIN
  :NEW.grpRelUpdatedAt := SYSDATE;
END trg_upd_grprels;
/
CREATE OR REPLACE TRIGGER trg_auto_nrOf AFTER INSERT OR UPDATE OR DELETE ON GROUPRELATIONS FOR EACH ROW
DECLARE
  v_count NUMBER(8,0);
  v_error VARCHAR2(2000);
BEGIN
  CASE
    WHEN INSERTING THEN
      IF (:NEW.canMngMbs NOT IN (0,1)) THEN
        v_error := 'You can only disable(0) or enable(1) a members ability to manage members from his group!';
        raise_application_error(-20999, v_error);
      ELSIF (:NEW.canUpdItm NOT IN (0,1)) THEN
        v_error := 'You can only disable(0) or enable(1) a members ability to manage items in his group!';
        raise_application_error(-20999, v_error);
      END IF;
      IF (:NEW.canMngMbs = 1) THEN
        UPDATE USERGROUPS SET nrOfManagers = nrOfManagers + 1, nrOfMembers = nrOfMembers + 1 WHERE uGroupId = :NEW.uGroupId;
      ELSE
        UPDATE USERGROUPS SET nrOfMembers = nrOfMembers + 1 WHERE uGroupId = :NEW.uGroupId;
      END IF;
    WHEN UPDATING('CANUPDITM') THEN
      IF (:NEW.canUpdItm NOT IN (0,1)) THEN
        v_error := 'You can only disable(0) or enable(1) a members ability to manage items in his group!';
        raise_application_error(-20999, v_error);
      END IF;
    WHEN UPDATING('CANMNGMBS') THEN
      v_error := 'You can only disable(0) or enable(1) a members ability to manage members from his group!';
      IF (:NEW.canMngMbs NOT IN (0,1)) THEN
        raise_application_error(-20999, v_error);
      END IF;
      IF (:NEW.canMngMbs = 0 AND :OLD.canMngMbs = 1) THEN
        UPDATE USERGROUPS SET nrOfManagers = nrOfManagers - 1 WHERE uGroupId = :NEW.uGroupId;
      END IF;
      IF (:NEW.canMngMbs = 1 AND :OLD.canMngMbs = 0) THEN
        UPDATE USERGROUPS SET nrOfManagers = nrOfManagers + 1 WHERE uGroupId = :NEW.uGroupId;
      END IF;
    WHEN DELETING THEN
      IF (:OLD.canMngMbs = 1) THEN
        UPDATE USERGROUPS SET nrOfManagers = nrOfManagers - 1, nrOfMembers = nrOfMembers - 1 WHERE uGroupId = :OLD.uGroupId;
      ELSE
        UPDATE USERGROUPS SET nrOfMembers = nrOfMembers - 1 WHERE uGroupId = :OLD.uGroupId;
      END IF;
  END CASE;
END trg_auto_nrOf;

/
CREATE SEQUENCE incr_itmgrpowns START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmgrpowns BEFORE INSERT ON ITEMGROUPOWNERSHIPS FOR EACH ROW
BEGIN
  :NEW.igOwnershipId := incr_itmgrpowns.NEXTVAL;
END trg_incr_itmgrpowns;
/


CREATE SEQUENCE incr_usrgrplogs START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_usrgrplogs BEFORE INSERT ON USERGROUPLOGS FOR EACH ROW
BEGIN
  :NEW.ugLogId := incr_usrgrplogs.NEXTVAL;
  :NEW.ugLogCreatedAt := SYSDATE;
END trg_incr_usrgrplogs;
/


CREATE SEQUENCE incr_itmgrplogs START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmgrplogs BEFORE INSERT ON ITEMGROUPLOGS FOR EACH ROW
BEGIN
  :NEW.igLogId := incr_itmgrplogs.NEXTVAL;
  :NEW.igLogCreatedAt := SYSDATE;
END trg_incr_itmgrplogs;
/


CREATE SEQUENCE incr_usrlogs START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_usrlogs BEFORE INSERT ON USERLOGS FOR EACH ROW
BEGIN
  :NEW.uLogId := incr_usrlogs.NEXTVAL;
  :NEW.uLogCreatedAt := SYSDATE;
END trg_incr_usrlogs;
/


CREATE SEQUENCE incr_itmlogs START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmlogs BEFORE INSERT ON ITEMLOGS FOR EACH ROW
BEGIN
  :NEW.iLogId := incr_itmlogs.NEXTVAL;
  :NEW.iLogCreatedAt := SYSDATE;
END trg_incr_itmlogs;
/


CREATE SEQUENCE incr_autorep START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_autorep BEFORE INSERT ON AUTOMATEDREPORTS FOR EACH ROW
BEGIN
  :NEW.reportId := incr_autorep.NEXTVAL;
  :NEW.rCreatedAt := SYSDATE;
END trg_incr_autorep;
/


CREATE SEQUENCE incr_notifs START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_notifs BEFORE INSERT ON NOTIFICATIONS FOR EACH ROW
BEGIN
  :NEW.ntfId := incr_notifs.NEXTVAL;
  :NEW.ntfCreatedAt := SYSDATE;
END trg_incr_notifs;
/


CREATE SEQUENCE incr_ugrpntfsrel START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_ugrpntfsrel BEFORE INSERT ON UGRPNTFRELATIONS FOR EACH ROW
BEGIN
  :NEW.usrgnRelationId := incr_ugrpntfsrel.NEXTVAL;
END trg_incr_ugrpntfsrel;
/


CREATE SEQUENCE incr_usrntfrel START WITH 1 INCREMENT BY 1;
/
CREATE OR REPLACE TRIGGER trg_incr_usrntfrel BEFORE INSERT ON USRNTFRELATIONS FOR EACH ROW
BEGIN
  :NEW.usrnRelationId := incr_usrntfrel.NEXTVAL;
END trg_incr_usrntfrel;
/
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
  v_adm_root_id USERACCS.USERID%TYPE;
  v_grp_id USERGROUPS.UGROUPID%TYPE;
BEGIN
  v_result := fct_addNewRootAdm('Esser', 'EsserTest1234','test@test.ro');
  IF (v_result = true) THEN
    v_result := fct_addRootGrps('Admins', 'Root admins group');
    IF (v_result = true) THEN
    v_result := fct_addRootGrps('Managers', 'Root managers group');
      IF (v_result = true) THEN
      v_result := fct_addRootGrps('Normal Users', 'Root normal users group');
        IF (v_result = true) THEN
          SELECT userId INTO v_adm_root_id FROM USERACCS WHERE userName = 'Esser' AND userEmail = 'test@test.ro';
          IF (SQL%FOUND) THEN
            COMMIT;
          ELSE
            ROLLBACK;
          END IF;
        ELSE
          ROLLBACK;
        END IF;
      ELSE
        ROLLBACK;
      END IF;
    ELSE
      ROLLBACK;
    END IF;
  ELSE
    ROLLBACK;
  END IF;
END;
/
COMMIT;
/
