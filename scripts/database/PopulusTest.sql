DECLARE
  TYPE user_group IS VARRAY(20) OF VARCHAR2(16);
  v_user_group user_group := user_group('Daniel.Melinte',
                                        'Andreea.Ghita',
                                        'Anca.Macovei',
                                        'Mihai.Paun',
                                        'Catrina.Petre',
                                        'Petru.Ghita',
                                        'Paul.Morar',
                                        'Ana.Costandache',
                                        'Biana.Croitoru',
                                        'Gigi.Capraru',
                                        'Carina.Moisescu',
                                        'George.Costache',
                                        'Costel.Negru',
                                        'Vasile.Anton',
                                        'Marcu.Bacauan',
                                        'Petrica.Bors',
                                        'Gheorghe.Cracan',
                                        'Viorel.Negroiu',
                                        'Alina.Petre',
                                        'Dorel.Mocofan');
  CURSOR useracc_line IS SELECT USERID FROM USERACCS WHERE USERID NOT IN (1,2,3);
  v_linie_useraccs useracc_line%ROWTYPE;
  v_count NUMBER(8,0) := 0;
  v_groupid NUMBER(8,0);
  v_sec_groupid NUMBER (8,0);
BEGIN
--   for v_i in v_user_group.first..v_user_group.last LOOP
--     INSERT INTO USERACCS (USERNAME, USEREMAIL, USERPASS, USERTYPE, USERSTATE, USERIMAGE)
--     VALUES (v_user_group(v_i), 'testAWSxQ'||v_i||'@gmail.ro', 'parola', '1', '1', 'undefined');
--   end loop;
--   INSERT INTO USERGROUPS (UGROUPNAME, UGROUPDESCRIPTION, NROFMEMBERS, NROFMANAGERS)
--   VALUES ('Group One', 'Just another test group for proof of fact...', 0, 0);
--   SELECT UGROUPID INTO v_groupid FROM USERGROUPS WHERE UGROUPNAME like 'Group One';
--   OPEN useracc_line;
--   WHILE (v_count < 15) LOOP
--     FETCH useracc_line INTO v_linie_useraccs;
--     EXIT WHEN useracc_line%NOTFOUND;
--     IF (v_count < 5) THEN
--       INSERT INTO GROUPRELATIONS (USERID, UGROUPID, CANUPDITM, CANMNGMBS)
--       VALUES (v_linie_useraccs.USERID, v_groupid, 1, 1);
--     ELSE
--       INSERT INTO GROUPRELATIONS (USERID, UGROUPID, CANUPDITM, CANMNGMBS)
--       VALUES (v_linie_useraccs.USERID, v_groupid, 1, 0);
--     END IF;
--     v_count := v_count + 1;
--   END LOOP;
  INSERT INTO ITEMGROUPS (IGROUPNAME, IGROUPDESCRIPTION)
  VALUES ('Coffee Products', 'Any and all coffee-based products of Group One!');
  SELECT IGROUPID INTO v_groupid FROM ITEMGROUPS WHERE IGROUPNAME LIKE 'Coffee Products';
  SELECT UGROUPID INTO v_sec_groupid FROM USERGROUPS WHERE UGROUPNAME LIKE 'Group One';
  INSERT INTO ITEMGROUPOWNERSHIPS (IGOWNERID, IGID) VALUES (v_sec_groupid, v_groupid);
  INSERT INTO ITEMS (ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, ITEMIMAGE)
    VALUES ('DonCafee Pachet 500g',
            'Pachet de cafea macinata ambalata in vid, marca DonCafe Alint Aroma, greutate 500 grame',
            15, v_groupid, 3, 'undefined');
  INSERT INTO ITEMS (ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, ITEMIMAGE)
  VALUES ('Jacobs Pachet 500g',
          'Pachet de cafea macinata ambalata in vid, marca Jacobs, greutate 500 grame',
          15, v_groupid, 3, 'undefined');
  INSERT INTO ITEMGROUPS (IGROUPNAME, IGROUPDESCRIPTION)
  VALUES ('Milk Products', 'Any and all milk-based products of Group One!');
  SELECT IGROUPID INTO v_groupid FROM ITEMGROUPS WHERE IGROUPNAME LIKE 'Milk Products';
  SELECT UGROUPID INTO v_sec_groupid FROM USERGROUPS WHERE UGROUPNAME LIKE 'Group One';
  INSERT INTO ITEMGROUPOWNERSHIPS (IGOWNERID, IGID) VALUES (v_sec_groupid, v_groupid);
  INSERT INTO ITEMS (ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, ITEMIMAGE)
  VALUES ('Lapte Milka 500ml',
          'Pachet de lapte UHT, marca Milka, gramaj 500 ml',
          15, v_groupid, 3, 'undefined');
  INSERT INTO ITEMS (ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, ITEMIMAGE)
  VALUES ('Lapte LaDorna 500ml',
          'Pachet de lapte UHT, marca LaDorna, gramaj 500 ml',
          15, v_groupid, 3, 'undefined');
  INSERT INTO ITEMGROUPS (IGROUPNAME, IGROUPDESCRIPTION)
  VALUES ('Choco Products', 'Any and all chocolate-based products of Group One!');
  SELECT IGROUPID INTO v_groupid FROM ITEMGROUPS WHERE IGROUPNAME LIKE 'Choco Products';
  SELECT UGROUPID INTO v_sec_groupid FROM USERGROUPS WHERE UGROUPNAME LIKE 'Group One';
  INSERT INTO ITEMGROUPOWNERSHIPS (IGOWNERID, IGID) VALUES (v_sec_groupid, v_groupid);
  INSERT INTO ITEMS (ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, ITEMIMAGE)
  VALUES ('Africana Tableta 500g',
          'Tableta de ciocolata ambalata in vid, marca Africana, greutate 500 grame',
          15, v_groupid, 3, 'undefined');
  INSERT INTO ITEMS (ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, ITEMIMAGE)
  VALUES ('Milka Tableta 500g',
          'Tableta de ciocolata ambalata in vid, marca Milka, greutate 500 grame',
          15, v_groupid, 3, 'undefined');
end;