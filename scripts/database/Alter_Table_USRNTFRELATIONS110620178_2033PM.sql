ALTER TABLE USRNTFRELATIONS ADD (usrnNIsRead  NUMBER(*,0) DEFAULT 0);
ALTER TABLE USRNTFRELATIONS ADD CONSTRAINT not_null_ntfIsRead CHECK (usrnNIsRead IS NOT NULL);