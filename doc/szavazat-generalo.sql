  INSERT INTO j_szavazatok  VALUES (0, 8, 9, 0, 0, 21, ROUND(RAND()*2 + 1) , 0, 0, 0, 0, 0, 578674);
  INSERT INTO j_szavazatok  VALUES (0, 8, 9, 0, 0, 22, ROUND(RAND()*2 + 1) , 0, 0, 0, 0, 0, 578674);
  INSERT INTO j_szavazatok  VALUES (0, 8, 9, 0, 0, 23, ROUND(RAND()*2 + 1) , 0, 0, 0, 0, 0, 578674);
UPDATE j_szavazatok SET szavazo_id = 941 WHERE szavazo_id = 0;
  UPDATE j_szavazatok SET user_id = szavazo_id WHERE user_id = 0;




