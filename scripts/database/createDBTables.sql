-- Creation of the database; Creation of Tables with the previously-created ORACLE Account Phase (step 2);
-- SET SERVEROUTPUT ON;
DECLARE
  v_i NUMBER(8,0);
  v_j NUMBER(8,0);
  v_sql_drop VARCHAR2(1000);
  TYPE obj_name_arr IS VARRAY(15) OF VARCHAR2(100);
  v_tbl_name_arr obj_name_arr := obj_name_arr('USERACCS', 'ITEMGROUPS', 'ITEMS', 'USERGROUPS', 'GROUPRELATIONS', 'ITEMOWNERSHIPS', 'ITEMGROUPOWNERSHIPS', 'USERGROUPLOGS', 'ITEMGROUPLOGS', 'USERLOGS', 'ITEMLOGS', 'AUTOMATEDREPORTS', 'NOTIFICATIONS', 'UGRPNTFRELATIONS', 'USRNTFRELATIONS');
  v_seq_name_arr obj_name_arr := obj_name_arr('INCR_USERACCS', 'INCR_ITMGRPS', 'INCR_ITMS', 'INCR_USERGRPS', 'INCR_GRPRELS', 'INCR_ITMOWNS', 'INCR_ITMGRPOWNS', 'INCR_USRGRPLOGS', 'INCR_ITMGRPLOGS', 'INCR_USRLOGS', 'INCR_ITMLOGS', 'INCR_AUTOREP', 'INCR_NOTIFS', 'INCR_UGRPNTFSREL', 'INCR_USRNTFREL');
  v_trg_name_arr obj_name_arr := obj_name_arr('TRG_INCR_USERACCS', 'TRG_INCR_ITMGRPS', 'TRG_INCR_ITMS', 'TRG_INCR_USERGRPS', 'TRG_INCR_GRPRELS', 'TRG_INCR_ITMOWNS', 'TRG_INCR_ITMGRPOWNS', 'TRG_INCR_USRGRPLOGS', 'TRG_INCR_ITMGRPLOGS', 'TRG_INCR_USRLOGS', 'TRG_INCR_ITMLOGS', 'TRG_INCR_AUTOREP', 'TRG_INCR_NOTIFS', 'TRG_INCR_UGRPNTFSREL', 'TRG_INCR_USRNTFREL');
BEGIN
  FOR v_i IN v_tbl_name_arr.FIRST..v_tbl_name_arr.LAST LOOP
    IF (v_tbl_name_arr.EXISTS(v_i)) THEN
      BEGIN
        v_sql_drop := 'DROP TABLE ' || upper(v_tbl_name_arr(v_i)) || ' CASCADE CONSTRAINTS PURGE';
        EXECUTE IMMEDIATE(v_sql_drop);
        DBMS_OUTPUT.PUT_LINE('Successfully droped table ' || v_tbl_name_arr(v_i) || '!');
      EXCEPTION
        WHEN OTHERS THEN
          null;
      END;
    END IF;
  END LOOP;
  DBMS_OUTPUT.PUT_LINE('All table names have been cleaned');
  FOR v_j IN v_seq_name_arr.FIRST..v_seq_name_arr.LAST LOOP
    --DBMS_OUTPUT.PUT_LINE('WTF WTF? v_j = ' || v_j);
    --DBMS_OUTPUT.PUT_LINE(v_seq_name_arr(v_j));
    IF (v_seq_name_arr.EXISTS(v_j)) THEN
      BEGIN
        v_sql_drop := 'DROP SEQUENCE ' || UPPER(v_seq_name_arr(v_j));
        EXECUTE IMMEDIATE(v_sql_drop);
        DBMS_OUTPUT.PUT_LINE('Successfully droped sequence ' || UPPER(v_seq_name_arr(v_j)) || '!');
      EXCEPTION
        WHEN OTHERS THEN
          null;
      END;
    END IF;
  END LOOP;
  DBMS_OUTPUT.PUT_LINE('All sequence names have been cleaned');
END;
--/
--CREATE OR REPLACE PROCEDURE prc_drop_all_obj(p_number NUMBER, p_type IN VARCHAR2)
--AS
--  v_i NUMBER := p_number;
--  v_type VARCHAR2(50) := p_type;
--  v_sql_drop VARCHAR2(1000);
--  TYPE obj_name_arr IS VARRAY(15) OF VARCHAR2(100);
--  v_tbl_name_arr obj_name_arr := obj_name_arr('USERACCS', 'ITEMGROUPS', 'ITEMS', 'USERGROUPS', 'GROUPRELATIONS', 'ITEMOWNERSHIPS', 'ITEMGROUPOWNERSHIPS', 'USERGROUPLOGS', 'ITEMGROUPLOGS', 'USERLOGS', 'ITEMLOGS', 'AUTOMATEDREPORTS', 'NOTIFICATIONS', 'UGRPNTFRELATIONS', 'USRNTFRELATIONS');
--  v_seq_name_arr obj_name_arr := obj_name_arr('INCR_USERACCS', 'INCR_ITMGRPS', 'INCR_ITMS', 'INCR_USERGRPS', 'INCR_GRPRELS', 'INCR_ITMOWNS', 'INCR_ITMGRPOWNS', 'INCR_USRGRPLOGS', 'INCR_ITMGRPLOGS', 'INCR_USRLOGS', 'INCR_ITMLOGS', 'INCR_AUTOREP', 'INCR_NOTIFS', 'INCR_UGRPNTFSREL', 'INCR_USRNTFREL');
--  v_trg_name_arr obj_name_arr := obj_name_arr('TRG_INCR_USERACCS', 'TRG_INCR_ITMGRPS', 'TRG_INCR_ITMS', 'TRG_INCR_USERGRPS', 'TRG_INCR_GRPRELS', 'TRG_INCR_ITMOWNS', 'TRG_INCR_ITMGRPOWNS', 'TRG_INCR_USRGRPLOGS', 'TRG_INCR_ITMGRPLOGS', 'TRG_INCR_USRLOGS', 'TRG_INCR_ITMLOGS', 'TRG_INCR_AUTOREP', 'TRG_INCR_NOTIFS', 'TRG_INCR_UGRPNTFSREL', 'TRG_INCR_USRNTFREL');
--BEGIN
--  IF (v_i < 1 or v_i > 16) THEN
--    raise_application_error(-20998, 'Bad number ("' || v_i || '")  selected! Only numbers allowed are from 1 to 15!');
--  END IF;
--  IF (lower(v_type) LIKE 'all_tables')  THEN
--    v_type := 'all_tables';
--    WHILE (v_i <= v_tbl_name_arr.COUNT AND v_type LIKE 'all_tables') LOOP
--      IF (v_tbl_name_arr.EXISTS(v_i)) THEN
--        v_sql_drop := 'DROP TABLE ' || v_tbl_name_arr(v_i) || ' CASCADE CONSTRAINTS PURGE';
--        EXECUTE IMMEDIATE(v_sql_drop);
--        DBMS_OUTPUT.PUT_LINE('Successfully droped table ' || v_tbl_name_arr(v_i) || '!');
--        v_i := v_i + 1;
--      END IF;
--    END LOOP;
--    DBMS_OUTPUT.PUT_LINE('All table names have been cleaned');
--  ELSIF (lower(v_type) LIKE 'all_sequences') THEN
--    v_type := 'all_sequences';
--    WHILE (v_i <= v_seq_name_arr.COUNT AND v_type LIKE 'all_sequences') LOOP
--      IF (v_seq_name_arr.EXISTS(v_i)) THEN
--        v_sql_drop := 'DROP SEQUENCE ' || UPPER(v_seq_name_arr(v_i) || '');
--        EXECUTE IMMEDIATE(v_sql_drop);
--        DBMS_OUTPUT.PUT_LINE('Successfully droped sequence ' || v_seq_name_arr(v_i) || '!');
--        v_i := v_i + 1;
--      END IF;
--    END LOOP;
--  ELSE
--    raise_application_error(-20999, 'Bad type ("' || lower(v_type) || '")  selected! Only "all_tables" or "all_sequences" is allowed!');
--  END IF;
--  DBMS_OUTPUT.PUT_LINE('Procedure finished!');
--  EXCEPTION
--  WHEN OTHERS THEN
--    IF (SQLCODE = -942 OR SQLCODE = -2289 OR SQLCODE = -6512) THEN
--      prc_drop_all_obj(v_i + 1, v_type);
--    ELSE
--      raise;
--    END IF;
--END prc_drop_all_obj;
--/
--SET SERVEROUTPUT ON;
--BEGIN
--  prc_drop_all_obj(1, 'all_tables');
--  prc_drop_all_obj(1, 'all_sequences');
--END;
/
CREATE TABLE USERACCS(
  userId NUMBER(*,0) NOT NULL,
  userName VARCHAR2(16) NOT NULL,
  userEmail VARCHAR2(48) NOT NULL,
  userPass VARCHAR2(512) NOT NULL,
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
  usrnNIsRead         NUMBER(*,0) DEFAULT 0,
  CONSTRAINT pk_usrnRelationId PRIMARY KEY (usrnRelationId),
  CONSTRAINT not_null_ntfIsRead CHECK (usrnNIsRead IS NOT NULL),
  CONSTRAINT fk_usrnNotifiedAccId FOREIGN KEY (usrnNotifiedAccId) REFERENCES USERACCS (userId),
  CONSTRAINT fk_usrnNotificationId FOREIGN KEY (usrnNotificationId) REFERENCES NOTIFICATIONS (ntfId)
)
/
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
COMMIT
/

