CREATE TABLE IF NOT EXISTS j_adausers (
	adaid VARCHAR(64),
	userid INT(11),
	KEY index1 (adaid),
	KEY index2 (userid)	
);
INSERT INTO j_adausers
SELECT DISTINCT SUBSTR(email,1,14),id FROM j_users WHERE email LIKE "%elovalasztok%" ORDER BY id; 
