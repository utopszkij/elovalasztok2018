/* SzabóKároly féle szavazási teszt csv betöltése */
DROP TABLE IF EXISTS w_teszt;

CREATE TABLE w_teszt(
i INT(11) AUTO_INCREMENT KEY,
a INT(11),
b INT(11),
c INT(11),
d INT(11),
e INT(11),
filler CHAR(1));

/* most kell a csv -t ebbe a munkafile-ba betölteni */

INSERT INTO j_szavazatok (temakor_id, szavazas_id, szavazo_id, alternativa_id, pozicio)
SELECT 8, 11, i, 33, a FROM w_teszt;

INSERT INTO j_szavazatok (temakor_id, szavazas_id, szavazo_id, alternativa_id, pozicio)
SELECT 8, 11, i, 34, b FROM w_teszt;

INSERT INTO j_szavazatok (temakor_id, szavazas_id, szavazo_id, alternativa_id, pozicio)
SELECT 8, 11, i, 35, c FROM w_teszt;

INSERT INTO j_szavazatok (temakor_id, szavazas_id, szavazo_id, alternativa_id, pozicio)
SELECT 8, 11, i, 36, d FROM w_teszt;

INSERT INTO j_szavazatok (temakor_id, szavazas_id, szavazo_id, alternativa_id, pozicio)
SELECT 8, 11, i, 37, e FROM w_teszt;




 


