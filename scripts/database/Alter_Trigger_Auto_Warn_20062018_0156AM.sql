create or replace trigger TRG_AUTO_WARN
  before update
  on ITEMS
  for each row
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
      WHEN DEFAULT THEN NULL;
    END CASE;
  END trg_auto_warn;
/
