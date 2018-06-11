-- Creation of the database; Create DB auto inserting triggers for column IDs, dates (createdAt/updatedAt) (Step 4);
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
CREATE OR REPLACE TRIGGER trg_incr_itmgrpowns BEFORE INSERT ON ITEMGROUPOWNERSHIPS FOR EACH ROW
BEGIN
  :NEW.igOwnershipId := incr_itmgrpowns.NEXTVAL;
END trg_incr_itmgrpowns;
/
CREATE OR REPLACE TRIGGER trg_incr_usrgrplogs BEFORE INSERT ON USERGROUPLOGS FOR EACH ROW
BEGIN
  :NEW.ugLogId := incr_usrgrplogs.NEXTVAL;
  :NEW.ugLogCreatedAt := SYSDATE;
END trg_incr_usrgrplogs;
/
CREATE OR REPLACE TRIGGER trg_incr_itmgrplogs BEFORE INSERT ON ITEMGROUPLOGS FOR EACH ROW
BEGIN
  :NEW.igLogId := incr_itmgrplogs.NEXTVAL;
  :NEW.igLogCreatedAt := SYSDATE;
END trg_incr_itmgrplogs;
/
CREATE OR REPLACE TRIGGER trg_incr_usrlogs BEFORE INSERT ON USERLOGS FOR EACH ROW
BEGIN
  :NEW.uLogId := incr_usrlogs.NEXTVAL;
  :NEW.uLogCreatedAt := SYSDATE;
END trg_incr_usrlogs;
/
CREATE OR REPLACE TRIGGER trg_incr_itmlogs BEFORE INSERT ON ITEMLOGS FOR EACH ROW
BEGIN
  :NEW.iLogId := incr_itmlogs.NEXTVAL;
  :NEW.iLogCreatedAt := SYSDATE;
END trg_incr_itmlogs;
/
CREATE OR REPLACE TRIGGER trg_incr_autorep BEFORE INSERT ON AUTOMATEDREPORTS FOR EACH ROW
BEGIN
  :NEW.reportId := incr_autorep.NEXTVAL;
  :NEW.rCreatedAt := SYSDATE;
END trg_incr_autorep;
/
CREATE OR REPLACE TRIGGER trg_incr_notifs BEFORE INSERT ON NOTIFICATIONS FOR EACH ROW
BEGIN
  :NEW.ntfId := incr_notifs.NEXTVAL;
  :NEW.ntfCreatedAt := SYSDATE;
END trg_incr_notifs;
/
CREATE OR REPLACE TRIGGER trg_incr_ugrpntfsrel BEFORE INSERT ON UGRPNTFRELATIONS FOR EACH ROW
BEGIN
  :NEW.usrgnRelationId := incr_ugrpntfsrel.NEXTVAL;
END trg_incr_ugrpntfsrel;
/
CREATE OR REPLACE TRIGGER trg_incr_usrntfrel BEFORE INSERT ON USRNTFRELATIONS FOR EACH ROW
BEGIN
  :NEW.usrnRelationId := incr_usrntfrel.NEXTVAL;
END trg_incr_usrntfrel;
/
COMMIT;
/
