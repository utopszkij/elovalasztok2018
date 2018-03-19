/* jelöltek kötegelt betöltése az elovalasztok web oldalra */


/* ajánló szervezet kódszámok (a @cimkeid kitöltéséhez kell) 
"2","MSZP"
"3","LMP"
"4","Párbeszéd"
"5","Együtt"
"6","FIDESZ"
"7","KDNP"
"8","Jobbik"
"9","DK"
"10","Liberálisok"
"11","MOMA"
"12","Momentum"
"13","Választói mozgalom"
"14","FÜGGETLEN"
"15","MSZDP"
"16","BAL párt"
"17","MKP"
"18","Európai baloldal"
"19","Kétfarkú Kutya Párt"
"20","Kalózpárt"
"21","EP"
*/

SET @cimkeid = 9; /* ide ird be az ajánló szervezet kodszámát! */
SET @ajanlo = "DK"; /* ide ird be az ajánló szervezet nevét */
SET @ajanlourl = "http://dkp.hu"; /* ide ird be az ajanlo szervezet web oldal cimét */

drop table if exists betolt;

create table betolt (cid int(11), oevk varchar(80), nev varchar(80));

/* az ez alatti sorokban sql szintaxisban egy táblázat van, ebben CSAK ÉS KIZÁRÓLAG a névnél modosits! */
/* a táblázat oszlopai: kodszám, oevk megnevezése, jelölt neve */
/* a neveknek macskakörmökközött kell lenniük, ha egy OEVK -ban az adott ajánló nem indul akkor a név helyén "" legyen */


insert into betolt values
("152","Bács-Kiskun megye 01. sz. OEVK","ide jön a név, vagy üres ha itt nincs jelöltje az adott szervezetnek "),
("151","Bács-Kiskun megye 02. sz. OEVK",""),
("150","Bács-Kiskun megye 03. sz. OEVK",""),
("13","Bács-Kiskun megye 04. sz. OEVK", ""),
("14","Bács-Kiskun megye 05. sz. OEVK", ""),
("15","Bács-Kiskun megye 06. sz. OEVK", ""),
("16","Baranya megye 01. sz. OEVK",""),
("17","Baranya megye 02. sz. OEVK",""),
("18","Baranya megye 03. sz. OEVK",""),
("19","Baranya megye 04. sz. OEVK",""),
("26","Békés megye 01. sz. OEVK",""),
("27","Békés megye 02. sz. OEVK",""),
("28","Békés megye 03. sz. OEVK",""),
("29","Békés megye 04. sz. OEVK",""),
("20","Borsod-Abaúj-Zemplén megye 01. sz. OEVK",""),
("153","Borsod-Abaúj-Zemplén megye 02. sz. OEVK",""),
("22","Borsod-Abaúj-Zemplén megye 03. sz. OEVK",""),
("154","Borsod-Abaúj-Zemplén megye 04. sz. OEVK",""),
("23","Borsod-Abaúj-Zemplén megye 05. sz. OEVK",""),
("24","Borsod-Abaúj-Zemplén megye 06. sz. OEVK",""),
("25","Borsod-Abaúj-Zemplén megye 07. sz. OEVK",""),
("131","Budapest 01. sz. OEVK",""),
("133","Budapest 02. sz. OEVK",""),
("134","Budapest 03. sz. OEVK",""),
("135","Budapest 04. sz. OEVK",""),
("136","Budapest 05. sz. OEVK",""),
("137","Budapest 06. sz. OEVK",""),
("138","Budapest 07. sz. OEVK",""),
("139","Budapest 08. sz. OEVK",""),
("140","Budapest 09. sz. OEVK",""),
("141","Budapest 10. sz. OEVK",""),
("142","Budapest 11. sz. OEVK",""),
("143","Budapest 12. sz. OEVK",""),
("144","Budapest 13. sz. OEVK",""),
("145","Budapest 14. sz. OEVK",""),
("146","Budapest 15. sz. OEVK",""),
("147","Budapest 16. sz. OEVK",""),
("148","Budapest 17. sz. OEVK",""),
("149","Budapest 18. sz. OEVK",""),
("47","Csongrád megye 01. sz. OEVK",""),
("48","Csongrád megye 02. sz. OEVK",""),
("49","Csongrád megye 03. sz. OEVK",""),
("50","Csongrád megye 04. sz. OEVK",""),
("51","Fejér megye 01. sz. OEVK",""),
("52","Fejér megye 02. sz. OEVK",""),
("53","Fejér megye 03. sz. OEVK",""),
("54","Fejér megye 04. sz. OEVK",""),
("55","Fejér megye 05. sz. OEVK",""),
("56","Győr-Moson-Sopron megye 01. sz. OEVK",""),
("57","Győr-Moson-Sopron megye 02. sz. OEVK",""),
("58","Győr-Moson-Sopron megye 03. sz. OEVK",""),
("59","Győr-Moson-Sopron megye 04. sz. OEVK",""),
("60","Győr-Moson-Sopron megye 05. sz. OEVK",""),
("61","Hajdú-Bihar megye 01. sz. OEVK",""),
("62","Hajdú-Bihar megye 02. sz. OEVK",""),
("63","Hajdú-Bihar megye 03. sz. OEVK",""),
("64","Hajdú-Bihar megye 04. sz. OEVK",""),
("65","Hajdú-Bihar megye 05. sz. OEVK",""),
("66","Hajdú-Bihar megye 06. sz. OEVK",""),
("67","Heves megye 01. sz. OEVK",""),
("68","Heves megye 02. sz. OEVK",""),
("69","Heves megye 03. sz. OEVK",""),
("70","Jász-Nagykun-Szolnok megye 01. sz. OEVK",""),
("71","Jász-Nagykun-Szolnok megye 02. sz. OEVK",""),
("72","Jász-Nagykun-Szolnok megye 03. sz. OEVK",""),
("73","Jász-Nagykun-Szolnok megye 04. sz. OEVK",""),
("74","Komárom-Esztergom megye 01. sz. OEVK",""),
("75","Komárom-Esztergom megye 02. sz. OEVK",""),
("76","Komárom-Esztergom megye 03. sz. OEVK",""),
("78","Nógrád megye 01. sz. OEVK",""),
("79","Nógrád megye 02. sz. OEVK",""),
("80","Pest megye 01. sz. OEVK",""),
("81","Pest megye 02. sz. OEVK",""),
("82","Pest megye 03. sz. OEVK",""),
("83","Pest megye 04. sz. OEVK",""),
("84","Pest megye 05. sz. OEVK",""),
("85","Pest megye 06. sz. OEVK",""),
("86","Pest megye 07. sz. OEVK",""),
("87","Pest megye 08. sz. OEVK",""),
("88","Pest megye 09. sz. OEVK",""),
("89","Pest megye 10. sz. OEVK",""),
("90","Pest megye 11. sz. OEVK",""),
("91","Pest megye 12. sz. OEVK",""),
("92","Somogy megye 01. sz. OEVK",""),
("93","Somogy megye 02. sz. OEVK",""),
("94","Somogy megye 03. sz. OEVK",""),
("95","Somogy megye 04. sz. OEVK",""),
("96","Szabolcs-Szatmár-Bereg megye 01. sz. OEVK",""),
("97","Szabolcs-Szatmár-Bereg megye 02. sz. OEVK",""),
("98","Szabolcs-Szatmár-Bereg megye 03. sz. OEVK",""),
("99","Szabolcs-Szatmár-Bereg megye 04. sz. OEVK",""),
("100","Szabolcs-Szatmár-Bereg megye 05. sz. OEVK",""),
("101","Szabolcs-Szatmár-Bereg megye 06. sz. OEVK",""),
("102","Tolna megye 01. sz. OEVK",""),
("103","Tolna megye 02. sz. OEVK",""),
("104","Tolna megye 03. sz. OEVK",""),
("105","Vas megye 01. sz. OEVK",""),
("106","Vas megye 02. sz. OEVK",""),
("107","Vas megye 03. sz. OEVK",""),
("108","Veszprém megye 01. sz. OEVK",""),
("109","Veszprém megye 02. sz. OEVK",""),
("110","Veszprém megye 03. sz. OEVK",""),
("111","Veszprém megye 04. sz. OEVK",""),
("112","Zala megye 01. sz. OEVK",""),
("113","Zala megye 02. sz. OEVK",""),
("114","Zala megye 03. sz. OEVK","")

/* ================ az ez alatti részt ne módositsd! ======================================== */

delete from betolt where nev = "";

SET @maxid = (select max(id) from j_content);

INSERT INTO j_content
SELECT 0, 
c0.asset_id, 
m.nev, 
REPLACE(CONVERT(CONCAT(c.id,m.nev) USING ASCII), '?', '-'), 
CONCAT('<img src="images/jeloltek/no_person.jpg" style="width:75%"," float:left"," margin:5px" />',@ajanlo,' jelölt<br /><br /><a href="',@ajanlourl,'" target="new">',@ajanlo,'</a><br />Ha a jelölt fényképet, további infokat küld az <a href="mailto:info@elovalasztok.hu">info@elovalasztok.hu</a> címre, akkor azokat feltesszük ide.'), 
'', 
1, 
c.id, 
NOW(), 
c0.created_by, 
c0.created_by_alias,  
NOW(), 
c0.created_by,  
0, 
"1900-01-01 00:00",  
c0.publish_up, 
"220-12-31 23:59", 
'', 
'', 
c0.attribs, 
c0.version,
c0.ordering, 
c0.metakey, 
c0.metadesc, 
c0.access, 
c0.hits, 
c0.metadata,
c0.featured, 
c0.language, 
c0.xreference
FROM betolt m
INNER JOIN j_categories c ON c.title = m.oevk and c.parent_id = 8
INNER JOIN j_content c0 ON c0.id = 47
WHERE c.id <> 131;

DELETE FROM j_contentitem_tag_map WHERE content_item_id > @maxid;
SET @w = (select max(core_content_id) from j_contentitem_tag_map);
INSERT INTO j_contentitem_tag_map
SELECT 'com_content.article', c.id - (@maxid - @w), c.id, @cimkeid, NOW(), 1
FROM j_content c
WHERE c.id > @maxid;

