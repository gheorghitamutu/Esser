--DROP USER EsseR;
--/
-- CREATE DB USER 
--CREATE USER EsseR IDENTIFIED BY 118887MgT ;
-- SET HIS QUOTAS
--ALTER USER EsseR QUOTA UNLIMITED ON USERS;
-- SET HIS ROLES
--GRANT "CONNECT" TO EsseR ;
--ALTER USER EsseR DEFAULT ROLE "CONNECT";
-- SET HIS SYSTEM PRIVILEGES
--GRANT CREATE LIBRARY TO EsseR ;
--GRANT CREATE TRIGGER TO EsseR ;
--GRANT CREATE ANY DIRECTORY TO EsseR ;
--GRANT CREATE MATERIALIZED VIEW TO EsseR ;
--GRANT CREATE INDEXTYPE TO EsseR ;
--GRANT CREATE VIEW TO EsseR ;
--GRANT CREATE SESSION TO EsseR ;
--GRANT CREATE RULE TO EsseR ;
--GRANT CREATE TABLE TO EsseR ;
--GRANT CREATE TYPE TO EsseR ;
--GRANT CREATE TABLESPACE TO EsseR ;
--GRANT CREATE SYNONYM TO EsseR ;
--GRANT CREATE SEQUENCE TO EsseR ;
--GRANT CREATE RULE SET TO EsseR ;
--GRANT CREATE PROCEDURE TO EsseR ;
--GRANT EXECUTE ON UTL_FILE TO EsseR;
--/
-- CHANGE CONNECTION TO THE NEWLY CREATED US;
-- Creation of the database; Need to remove the DROP TABLE STATEMENTS before live production
DROP TABLE USERACCS CASCADE CONSTRAINTS PURGE;
DROP TABLE ITEMGROUPS CASCADE CONSTRAINTS PURGE;
DROP TABLE ITEMS CASCADE CONSTRAINTS PURGE;
DROP TABLE USERGROUPS CASCADE CONSTRAINTS PURGE;
DROP TABLE GROUPRELATIONS CASCADE CONSTRAINTS PURGE;
DROP TABLE ITEMOWNERSHIPS CASCADE CONSTRAINTS PURGE;
DROP TABLE ITEMGROUPLOGS CASCADE CONSTRAINTS PURGE;
DROP TABLE ITEMGROUPOWNERSHIPS CASCADE CONSTRAINTS PURGE;
DROP TABLE ITEMLOGS CASCADE CONSTRAINTS PURGE;
DROP TABLE USERGROUPLOGS CASCADE CONSTRAINTS PURGE;
DROP TABLE USERLOGS CASCADE CONSTRAINTS PURGE;
DROP TABLE NOTIFICATIONS CASCADE CONSTRAINTS PURGE;
DROP TABLE AUTOMATEDREPORTS CASCADE CONSTRAINTS PURGE;
DROP TABLE UGRPNTFRELATIONS CASCADE CONSTRAINTS PURGE;
DROP TABLE UNTFRELATIONS CASCADE CONSTRAINTS PURGE;
/
CREATE TABLE USERACCS(
  userId NUMBER(*,0) NOT NULL,
  userName VARCHAR2(48) NOT NULL,
  userEmail VARCHAR2(48) NOT NULL,
  userPass VARCHAR2(48) NOT NULL,
  userType  NUMBER(1) DEFAULT 0,
  userState NUMBER(1) DEFAULT 1,
  userImage varchar2(256) DEFAULT NULL,
  userCreatedAt DATE NOT NULL,
  userUpdatedAt DATE NOT NULL,  
  CONSTRAINT pk_userId PRIMARY KEY(userId),
  CONSTRAINT unq_useremail UNIQUE(userEmail),
  CONSTRAINT not_null_usertype CHECK (userType is not null),
  CONSTRAINT not_null_userstate CHECK (userState is not null)
);
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
);
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
);
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
);
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
);
/
CREATE TABLE ITEMOWNERSHIPS(
  iOwnershipId NUMBER(*,0) NOT NULL,
  iOwnerId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_iownId PRIMARY KEY (iOwnershipId),
  CONSTRAINT fk_ownerId FOREIGN KEY (iOwnerId) REFERENCES USERGROUPS(uGroupId)
);
/
CREATE TABLE ITEMGROUPOWNERSHIPS(
  igOwnershipId NUMBER(*,0) NOT NULL,
  igOwnerId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_igownId PRIMARY KEY (igOwnershipId),
  CONSTRAINT fk_igownerId FOREIGN KEY (igOwnerId) REFERENCES USERGROUPS(uGroupId)
);
/
CREATE TABLE USERGROUPLOGS(
  ugLogId NUMBER(*,0) NOT NULL,
  ugLogDescription VARCHAR2(2000) DEfAULT 'Log about an user group',
  ugLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  ugLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_ugLogId PRIMARY KEY (ugLogId),
  CONSTRAINT not_null_ugdscr CHECK (ugLogDescription IS NOT NULL),
  CONSTRAINT not_null_ugip CHECK (ugLogSourceIP IS NOT NULL)
);
/
CREATE TABLE ITEMGROUPLOGS(
  igLogId NUMBER(*,0) NOT NULL,
  igLogDescription VARCHAR2(2000) DEfAULT 'Log about an item group',
  igLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  igLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_igLogId PRIMARY KEY (igLogId),
  CONSTRAINT not_null_igdscr CHECK (igLogDescription IS NOT NULL),
  CONSTRAINT not_null_igip CHECK (igLogSourceIP IS NOT NULL)
);
/
CREATE TABLE USERLOGS(
  uLogId NUMBER(*,0) NOT NULL,
  uLogDescription VARCHAR2(2000) DEfAULT 'Log about a user',
  uLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  uLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_uLogId PRIMARY KEY (uLogId),
  CONSTRAINT not_null_udscr CHECK (uLogDescription IS NOT NULL),
  CONSTRAINT not_null_uip CHECK (uLogSourceIP IS NOT NULL)
);
/
CREATE TABLE ITEMLOGS(
  iLogId NUMBER(*,0) NOT NULL,
  iLogDescription VARCHAR2(2000) DEfAULT 'Log about an item',
  iLogSourceIP VARCHAR2(15) DEFAULT 'XXX.XXX.XXX.XXX',
  iLogCreatedAt DATE NOT NULL,
  CONSTRAINT pk_iLogId PRIMARY KEY (iLogId),
  CONSTRAINT not_null_ildscr CHECK (iLogDescription IS NOT NULL),
  CONSTRAINT not_null_ilip CHECK (iLogSourceIP IS NOT NULL)
);
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
);
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
);
/
CREATE TABLE UGRPNTFRELATIONS(
  usrgnRelationId NUMBER(*,0) NOT NULL,
  usrgnNotificationId NUMBER(*,0) NOT NULL,
  usrgnNotifiedGroupId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_ugNRelId PRIMARY KEY (usrgnRelationId),
  CONSTRAINT fk_usrgnNotifiedGroupId FOREIGN KEY (usrgnNotifiedGroupId) REFERENCES USERGROUPS (uGroupId),
  CONSTRAINT fk_usrgnNotificationId  FOREIGN KEY (usrgnNotificationId) REFERENCES NOTIFICATIONS (ntfId)
);
/
CREATE TABLE USRNTFRELATIONS(
  usrnRelationId NUMBER(*,0) NOT NULL,
  usrnNotifiedAccId NUMBER(*,0) NOT NULL,
  usrnNotificationId NUMBER(*,0) NOT NULL,
  CONSTRAINT pk_usrnRelationId PRIMARY KEY (usrnRelationId),
  CONSTRAINT fk_usrnNotifiedAccId FOREIGN KEY (usrnNotifiedAccId) REFERENCES USERACCS (userId),
  CONSTRAINT fk_usrnNotificationId FOREIGN KEY (usrnNotificationId) REFERENCES NOTIFICATIONS (ntfId)
);
/
