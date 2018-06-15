DECLARE
  TYPE user_group IS VARRAY(20) OF VARCHAR2(16);
  v_user_group user_group := user_group('Daniel Melinte',
                                        'Andreea Ghita',
                                        'Anca Macovei',
                                        'Mihai Paun',
                                        'Catrina Petre',
                                        'Petru Ghita',
                                        'Paul Morar',
                                        'Ana Costandache',
                                        'Biana Croitoru',
                                        'Gigi Capraru',
                                        'Carina Moisescu',
                                        'George Costache',
                                        'Costel Negru',
                                        'Vasile Anton',
                                        'Marcu Bacauan',
                                        'Petrica Bors',
                                        'Gheorghe Cracan',
                                        'Viorel Negroiu',
                                        'Alina Petre',
                                        'Dorel Mocofan');
  CURSOR useracc_line IS SELECT * FROM USERACCS WHERE USERID NOT IN (1,2,3);
  v_linie_useraccs useracc_line%ROWTYPE;
BEGIN
  for v_i in v_user_group.first..v_user_group.last LOOP
    INSERT INTO USERACCS (USERNAME, USEREMAIL, USERPASS, USERTYPE, USERSTATE, USERIMAGE) VALUES (v_user_group(v_i),
                                                                                                 'testAWSxQ'||v_i||'@gmail.ro',
                                                                                                 'parola',
                                                                                                 '1',
                                                                                                 '1',
                                                                                                 'undefined');
  end loop;
end;