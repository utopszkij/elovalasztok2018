/* 
minta cikk content.id = 48
kategoria amibe az EOVK -k vannak  categories.id = 8
kategoria ahol a minta cikk van categoies.= 131
*/


/*fidesz KDNP jelölt sokszorozás */
/* ============================= */
select max(id) from j_content;  /* felirni !!!!!! */

INSERT INTO j_content
SELECT 	0 AS `id`, 
	`asset_id`, 
	`title`, 
	`alias`, 
	`introtext`, 
	`fulltext`, 
	`state`, 
	w.id catid, 
	`created`, 
	`created_by`, 
	`created_by_alias`, 
	`modified`, 
	`modified_by`, 
	`checked_out`, 
	"1970-01-01 00:00:00", 
	`publish_up`, 
	"2200-12-31 00:00:00", 
	`images`, 
	`urls`, 
	`attribs`, 
	`version`, 
	`ordering`, 
	`metakey`, 
	`metadesc`, 
	`access`, 
	`hits`, 
	`metadata`, 
	`featured`, 
	`language`, 
	`xreference`
FROM  j_content c ,
(SELECT id 
FROM j_categories
WHERE parent_id = 8 AND id <> 133  /* OEVK gyüjtő kategoria, a minta cikk a 133 kategorriában van */
) w
WHERE c.id=613; /* minta cikk */

/* a 805 -es szám helyére az amit fentebb felirtunk !!!!!!, */

DELETE FROM j_contentitem_tag_map WHERE content_item_id > 805;  /* <<<<<< javitani */
INSERT INTO j_contentitem_tag_map
SELECT 'com_content.article', c.id, c.id, 6, NOW(), 1
FROM j_content c
WHERE c.id > 805;  /* <<<<<< javitani */

INSERT INTO j_contentitem_tag_map
SELECT 'com_content.article', c.id, c.id, 7, NOW(), 1
FROM j_content c
WHERE c.id > 805; /* <<<<<< javitani */

/* jobbik jelölt sokszorozás */
/* ========================= */
select max(id) from j_content;  /* felirni !!!!!! */

INSERT INTO j_content
SELECT 	0 AS `id`, 
	`asset_id`, 
	`title`, 
	`alias`, 
	`introtext`, 
	`fulltext`, 
	`state`, 
	w.id catid, 
	`created`, 
	`created_by`, 
	`created_by_alias`, 
	`modified`, 
	`modified_by`, 
	`checked_out`, 
	"1970-01-01 00:00:00", 
	`publish_up`, 
	"2200-12-31 00:00:00", 
	`images`, 
	`urls`, 
	`attribs`, 
	`version`, 
	`ordering`, 
	`metakey`, 
	`metadesc`, 
	`access`, 
	`hits`, 
	`metadata`, 
	`featured`, 
	`language`, 
	`xreference`
FROM  j_content c ,
(SELECT id 
FROM j_categories
WHERE parent_id = 8 AND id <> 133  /* OEVK gyüjtő kategoria, a minta cikk a 133 kategorriában van */
) w
WHERE c.id=612; /* minta cikk */
	
/* a 805 -es szám helyére az amit fentebb felirtunk !!!!!!, */

DELETE FROM j_contentitem_tag_map WHERE content_item_id > 805;  /* <<<<<< javitani */
INSERT INTO j_contentitem_tag_map
SELECT 'com_content.article', c.id, c.id, 8, NOW(), 1
FROM j_content c
WHERE c.id > 805;  /* <<<<<< javitani */







