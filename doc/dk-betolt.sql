drop table if exists dk;
create table if not exists dk (oevk varchar(80), nev varchar(120));

insert into dk values
("Bács-Kiskun megye 01. sz. OEVK","Kopping Rita"),
("Bács-Kiskun megye 05. sz. OEVK","Molnár László"),
("Baranya megye 02. sz. OEVK","Nagy Ferenc"),
("Baranya megye 03. sz. OEVK","Lukács János"),
("Békés megye 01. sz. OEVK","Nagy-Huszein Tibor"),
("Békés megye 02. sz. OEVK","Kondé Gábor"),
("Borsod-Abaúj-Zemplén megye 01. sz. OEVK","Debreczeni József"),
("Borsod-Abaúj-Zemplén megye 03. sz. OEVK","Varga Gergő"),
("Borsod-Abaúj-Zemplén megye 05. sz. OEVK","Záveczki Tibor"),
("Budapest 02. sz. OEVK","Gy. Németh Erzsébet"),
("Budapest 03. sz. OEVK","Bauer Tamás"),
("Budapest 04. sz. OEVK","Niedermüller Péter"),
("Budapest 05. sz. OEVK","Oláh Lajos"),
("Budapest 06. sz. OEVK","Ara-Kovács Attila"),
("Budapest 11. sz. OEVK","Varju László"),
("Budapest 12. sz. OEVK","Hajdu László"),
("Budapest 13. sz. OEVK","Nemes Gábor"),
("Csongrád megye 03. sz. OEVK","Eörsi Mátyás"),
("Fejér megye 01. sz. OEVK","Földi Judit"),
("Fejér megye 04. sz. OEVK","Mezei Zsolt"),
("Fejér megye 05. sz. OEVK","Szilveszterné Nyúli Ilona"),
("Győr-Moson-Sopron megye 01. sz. OEVK","Glázer Timea"),
("Győr-Moson-Sopron megye 03. sz. OEVK","Szabó Zoltán"),
("Hajdú-Bihar megye 02. sz. OEVK","Varga Zoltán"),
("Hajdú-Bihar megye 03. sz. OEVK","Káposznyák István"),
("Heves megye 01. sz. OEVK","Kertészné Kormos Noémi"),
("Jász-Nagykun-Szolnok megye 02. sz. OEVK","Gedei József"),
("Jász-Nagykun-Szolnok megye 03. sz. OEVK","Bodó Jánosné"),
("Komárom-Esztergom megye 02. sz. OEVK","Vadai Ágnes"),
("Nógrád megye 01. sz. OEVK","Kovács Zsolt"),
("Pest megye 02. sz. OEVK","Tóth Zoltán"),
("Pest megye 03. sz. OEVK","Király Miklós"),
("Pest megye 04. sz. OEVK","Krauze István"),
("Pest megye 05. sz. OEVK","Rónai Sándor"),
("Pest megye 07. sz. OEVK","Nyeste Andrea"),
("Pest megye 12. sz. OEVK","Szüdi János"),
("Somogy megye 01. sz. OEVK","László Imre"),
("Somogy megye 02. sz. OEVK","Remes Gábor"),
("Szabolcs-Szatmár-Bereg megye 02. sz. OEVK","Helmeczy László"),
("Szabolcs-Szatmár-Bereg megye 03. sz. OEVK","Rakoczki Dénesné"),
("Szabolcs-Szatmár-Bereg megye 05. sz. OEVK","Rácz Erika"),
("Tolna megye 02. sz. OEVK","Gecséné Slárku Szlivia"),
("Vas megye 03. sz. OEVK","Balogh Tibor"),
("Veszprém megye 02. sz. OEVK","Deák Istvánné"),
("Zala megye 02. sz. OEVK","Kovács Viktória"),
("Zala megye 03. sz. OEVK","Horváth Jácint");

SELECT count(*) FROM dk; /* FELIRNI !! */


SELECT MAX(id) FROM j_content; /* FELIRNI !! */

INSERT INTO j_content
SELECT 0, 
c0.asset_id, 
m.nev, 
REPLACE(CONVERT(CONCAT(c.id,m.nev) USING ASCII), '?', '-'), 
'<img src="images/jeloltek/no_person.jpg" style="width:75%"," float:left"," margin:5px" /> DK párt jelöltje.<br />Forrás: 
<a href="http://www.blikk.hu/aktualis/politika/megszereztuk-a-dk-szolidaritas-jeloltlistajat/28bpw14" 
target="new">blikk.hu</a><br />
<a href="http://dkp.hu" target="new">dk párt honlapja</a><br />
Ha a jelölt fényképet, további infokat küld az <a href="mailto:info@elovalasztok.hu">info@elovalasztok.hu</a> címre, akkor azokat feltesszük ide.', 
'', 
1, 
c.id, 
NOW(), 
c0.created_by, 
c0.created_by_alias,  
NOW(), 
c0.created_by,  
0, 
0,  
c0.publish_up, 
c0.publish_down, 
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
FROM dk m
INNER JOIN j_categories c ON c.title = m.oevk and c.parent_id = 8
INNER JOIN j_content c0 ON c0.id = 47
WHERE c.id <> 131;

/* a 805 -es szám helyére az amit fentebb felirtunk !!!!!!, 9 = DK id -je a tags rekordban */

DELETE FROM j_contentitem_tag_map WHERE content_item_id > 805;
INSERT INTO j_contentitem_tag_map
SELECT 'com_content.article', c.id, c.id, 9, NOW(), 1
FROM j_content c
WHERE c.id > 805;



