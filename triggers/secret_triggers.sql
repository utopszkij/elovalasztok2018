/* biztonsági triggerek
Biztonsági megjegyzések:
- a joomla_db_user -nek ne legyen joga a triggereket irini, listázni, modositani, törölni

*/


DELIMITER $$
DROP TRIGGER IF EXISTS `szavazatok_update_secret`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `szavazatok_update_secret` BEFORE UPDATE ON `ev_szavazatok`
    FOR EACH ROW BEGIN
		SIGNAL SQLSTATE '45000'  SET MESSAGE_TEXT = 'can not vote update';
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `szavazatok_delete_secret`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `szavazatok_delete_secret` BEFORE UPDATE ON `ev_szavazatok`
    FOR EACH ROW BEGIN
		SIGNAL SQLSTATE '45000'  SET MESSAGE_TEXT = 'can not vote delete';
    END$$
DELIMITER ;

/* az alábbi triggert csak a szavazás lezárasakor kell telepiteni */

DELIMITER $$
DROP TRIGGER IF EXISTS `szavazatok_insert_secret`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `szavazatok_insert_secret` BEFORE INSERT ON `ev_szavazatok`
    FOR EACH ROW BEGIN
		SIGNAL SQLSTATE '45000'  SET MESSAGE_TEXT = 'can not vote insert';
    END$$
DELIMITER ;




