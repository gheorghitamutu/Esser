-- Creation of the database; Need to remove the DROP TABLE STATEMENTS before live production
 DROP TABLE USERACCS CASCADE CONSTRAINTS PURGE
 /
 DROP TABLE ITEMGROUPS CASCADE CONSTRAINTS PURGE
 /
 DROP TABLE ITEMS CASCADE CONSTRAINTS PURGE
 /
 DROP TABLE USERGROUPS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE GROUPRELATIONS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE ITEMOWNERSHIPS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE ITEMGROUPOWNERSHIPS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE USERGROUPLOGS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE ITEMGROUPLOGS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE USERLOGS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE ITEMLOGS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE AUTOMATEDREPORTS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE NOTIFICATIONS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE UGRPNTFRELATIONS CASCADE CONSTRAINTS PURGE
/
 DROP TABLE USRNTFRELATIONS CASCADE CONSTRAINTS PURGE
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
CREATE OR REPLACE SEQUENCE incr_useraccs start with 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_useraccs BEFORE INSERT ON USERACCS FOR EACH ROW
BEGIN
  SELECT INCR_USERACCS.NEXTVAL INTO :NEW.USERID FROM DUAL;
END trg_incr_useraccs;
/
CREATE TABLE ITEMGROUPS(
  iGroupId NUMBER(*,0) NOT NULL,
  iGroupName VARCHAR2(48) DEFAULT 'Group of items',
  iGroupDescription VARCHAR2(2000) DEFAULT 'Description about group of items',
  iGroupCreatedAt DATE NOT NULL,
  iGroupUpdatedAt DATE NOT NULL,
  CONSTRAINT pk_iGroupId PRIMARY KEY(iGroupId),
  CONSTRAINT not_null_igrpname CHECK (iGroupName is not null),
  CONSTRAINT not_null_igrpdescrp CHECK (iGroupDescription is not null)
)
/
CREATE OR REPLACE SEQUENCE incr_itmgrps START WITH 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmgrps BEFORE INSERT ON ITEMGROUPS FOR EACH ROW
BEGIN
  SELECT INCR_ITMGRPS.NEXTVAL INTO :NEW.IGROUPID FROM DUAL;
END trg_incr_itmgrps;
/
CREATE TABLE ITEMS(
  itemId NUMBER(*,0) NOT NULL,
  itemName VARCHAR2(48) DEFAULT 'An items',
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
CREATE OR REPLACE SEQUENCE incr_itms START WITH 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itms BEFORE INSERT ON ITEMS FOR EACH ROW
BEGIN
  SELECT INCR_ITMS.NEXTVAL INTO :NEW.ITEMID FROM DUAL;
END trg_incr_itms;
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
  CONSTRAINT not_null_ugrpdescrp CHECK (uGroupDescription is not null)
)
/
CREATE OR REPLACE SEQUENCE incr_usergrps START WITH 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_usergrps BEFORE INSERT ON USERGROUPS FOR EACH ROW
BEGIN
  SELECT INCR_USERGRPS.NEXTVAL INTO :NEW.UGROUPID FROM DUAL;
END trg_incr_usergrps;
/
CREATE TABLE GROUPRELATIONS(
  relationId NUMBER(*,0) NOT NULL,
  userId NUMBER(*,0) NOT NULL,
  uGroupId NUMBER(*,0) NOT NULL,
  canUpdItm NUMBER(1) DEFAULT 1,
  canMngMbs NUMBER(1) DEFAULT 0,
  CONSTRAINT pk_relationId PRIMARY KEY(relationId),
  CONSTRAINT fk_rUserId FOREIGN KEY(userId) REFERENCES USERACCS(userId),
  CONSTRAINT fk_rGroupId FOREIGN KEY(uGroupId) REFERENCES USERGROUPS(uGroupId),
  CONSTRAINT not_null_canupditm CHECK (canUpdItm is not null),
  CONSTRAINT not_null_canmngmbs CHECK (canMngMbs is not null)
)
/
CREATE OR REPLACE SEQUENCE incr_grprels START WITH 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_grprels BEFORE INSERT ON GROUPRELATIONS FOR EACH ROW
BEGIN
  SELECT incr_grprels.NEXTVAL INTO :NEW.RELATIONID FROM DUAL;
END trg_incr_grprels;
/
CREATE TABLE ITEMOWNERSHIPS(
  iOwnershipId NUMBER(*,0) NOT NULL,
  iOwnerId NUMBER(*,0) NOT NULL,
  iId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_iownId PRIMARY KEY (iOwnershipId),
  CONSTRAINT fk_ownerId FOREIGN KEY (iOwnerId) REFERENCES USERACCS(userId),
  CONSTRAINT fk_owneditmId FOREIGN KEY (iId) REFERENCES ITEMS(itemId)
)
/
CREATE OR REPLACE SEQUENCE incr_itmowns START WITH 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmowns BEFORE INSERT ON ITEMOWNERSHIPS FOR EACH ROW
BEGIN
  SELECT INCR_ITMOWNS.NEXTVAL INTO :NEW.iOwnershipId FROM DUAL;
END trg_incr_itmowns;
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
CREATE OR REPLACE SEQUENCE incr_itmgrpowns START WITH 1 INCREMENT BY 1 MINVALUE 1;
/
CREATE OR REPLACE TRIGGER trg_incr_itmgrpowns BEFORE INSERT ON ITEMGROUPOWNERSHIPS FOR EACH ROW
BEGIN
  SELECT incr_itmgrpowns.NEXTVAL INTO :NEW.igOwnershipId FROM DUAL;
END trg_incr_itmgrpowns;
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
CREATE OR REPLACE FUNCTION prc_addRootAdm(p_username VARCHAR2, p_password VARCHAR2, p_email VARCHAR2, p_userImage VARCHAR2 DEFAULT 'undefined')
RETURN BOOLEAN
AS
  v_usernamae VARCHAR2(16);
  v_password VARCHAR2(16);
  v_email VARCHAR2(48);
  v_userImage VARCHAR2(256);
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
  exc_img_path_length exception;
  PRAGMA EXCEPTION_INIT(exc_img_path_length, -20006);
  exc_bad_email_format exception;
  PRAGMA EXCEPTION_INIT(exc_bad_email_format, -20007);
  exc_bad_image_path exception;
  PRAGMA EXCEPTION_INIT(exc_bad_image_path, -20008);
  v_sql_cmd VARCHAR2(2000);
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
  IF (length(p_userImage) < 12 or length(p_userImage) > 256) THEN
    raise exc_img_path_length;
  END IF;
  IF (REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') IS NOT NULL) THEN
    raise exc_non_alpnum_username;
  END IF;
  IF (REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') IS NOT NULL) THEN
    raise exc_non_alpnum_password;
  END IF;
  IF (REGEXP_SUBSTR(p_email,'[^a-zA-Z0-9@._$+*#!%&(){}[]:<>?-]+') IS NOT NULL) THEN
    raise exc_bad_email_format;
  END IF;
  IF (REGEXP_SUBSTR(p_userImage, '[^a-zA-Z0-9@._$+*#!%&(){}[]:<>?-]+') IS NOT NULL) THEN
    raise exc_bad_image_path
  END IF;
  
  EXECUTE IMMEDIATE();
  
  
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
  WHEN exc_img_path_length THEN
    raise_application_error(-20006, 'The provided image path length is: ' || length(p_userImage) || ' !. It needs to be between 12 and 256 alpha-numeric characters!');
  WHEN exc_bad_email_format THEN
    raise_application_error(-20007, 'Found illegal character in the provided email: "' || REGEXP_SUBSTR(p_username,'[^a-zA-Z0-9]+') || '" !');
  WHEN exc_bad_image_path THEN
    raise_application_error(-20008, 'Found illegal character in the provided image path: "' || REGEXP_SUBSTR(p_password,'[^a-zA-Z0-9]+') || '" !');
  WHEN OTHERS THEN  
    IF (SQLCODE = -1920) THEN
      EXECUTE IMMEDIATE('DROP USER ' || p_username || ' CASCADE');
      prc_esser_crt_root_user(p_username, p_password);
    ELSE
      RAISE;
    END IF; 
END prc_addRootAdm;
/
COMMIT;
/
