/* ags_map be is irni kell!   momentum=12 */

DELETE FROM j_content WHERE id > 182;

SELECT MAX(id) FROM j_content;

INSERT INTO j_content
SELECT 0, 
c0.asset_id, 
m.nev, 
REPLACE(CONVERT(CONCAT(c.id,m.nev) USING ASCII), '?', '-'), 
'<img src="images/jeloltek/no_person.jpg" style="width:75%; float:left; margin:5px" /> Momentum párt jelöltje.<br />Forrás: <a href="https://momentum.hu" target="new">https://momentum.hu</a><br />Ha a jelölt fényképet, további infokat küld az <a href="mailto:info@elovalasztok.hu">info@elovalasztok.hu</a> címre, akkor azokat feltesszük ide.', 
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
FROM momentum m
INNER JOIN j_categories c ON 
(c.title LIKE CONCAT(SUBSTRING_INDEX(m.oevk,' ',2),'%'))  AND c.parent_id = 8
INNER JOIN j_content c0 ON c0.id = 47
WHERE c.id <> 131
UNION
SELECT 0, 
c0.asset_id, 
m.nev, 
REPLACE(CONVERT(CONCAT(c.id,m.nev) USING ASCII) ,'?','-'), 
'<img src="images/jeloltek/no_person.jpg" style="width:75%; float:left; margin:5px" /> Momentum párt jelöltje.<br />Forrás: <a href="https://momentum.hu" target="new">https://momentum.hu</a><br />Ha a jelölt fényképet, további infokat küld az <a href="mailto:info@elovalasztok.hu">info@elovalasztok.hu</a> címre, akkor azokat feltesszük ide.', 
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
FROM momentum m
INNER JOIN j_categories c ON 
(c.title LIKE CONCAT(SUBSTRING_INDEX(m.oevk,' ',1),'%',SUBSTRING_INDEX(m.oevk,' ',-1),'%'))   AND c.parent_id = 8
INNER JOIN j_content c0 ON c0.id = 47
WHERE c.id <> 131;

DELETE FROM j_contentitem_tag_map WHERE content_item_id > 182;

INSERT INTO j_contentitem_tag_map
SELECT 'type_content_article', 20, c.id, 12, NOW(), 1
FROM j_content c
WHERE c.id > 182;

;


