/* naplozó triggerek
Biztonsági megjegyzések:
- a joomla_db_user -nek ne legyen joga a triggereket irini, listázni, modositani, törölni
- a j_pvoks_logs file -t a joomla_db_user csak irhatja és listázhatja
*/

/* questions */  
DELIMITER $$
DROP TRIGGER IF EXISTS `question_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `question_insert_log` AFTER INSERT
    ON `j_pvoks_questions`
    FOR EACH ROW BEGIN
		SET @S = CONCAT(NEW.id,";", 
		NEW.category_id,";", 
		NEW.question_type,";", 
		NEW.title,";", 
		NEW.alias,";", 
		NEW.introtext,";", 
		NEW.fulltext,";", 
		NEW.state,";", 
		NEW.secret,";", 
		NEW.publicvote,";", 
		NEW.accredite_enabled,";", 
		NEW.target_category_id,";", 
		NEW.termins,";", 
		NEW.optvalid,";", 
		NEW.debatevalid,";", 
		NEW.votevalid,";", 
		NEW.created,";", 
		NEW.created_by,";", 
		NEW.modified,";", 
		NEW.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"question",NEW.id, NEW.created_by, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `question_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `question_update_log` AFTER UPDATE
    ON `j_pvoks_questions`
    FOR EACH ROW BEGIN
		SET @S = CONCAT(NEW.id,";", 
		NEW.category_id,";", 
		NEW.question_type,";", 
		NEW.title,";", 
		NEW.alias,";", 
		NEW.introtext,";", 
		NEW.fulltext,";", 
		NEW.state,";", 
		NEW.secret,";", 
		NEW.publicvote,";", 
		NEW.accredite_enabled,";", 
		NEW.target_category_id,";", 
		NEW.termins,";", 
		NEW.optvalid,";", 
		NEW.debatevalid,";", 
		NEW.votevalid,";", 
		NEW.created,";", 
		NEW.created_by,";", 
		NEW.modified,";", 
		NEW.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"question",NEW.id, NEW.created_by, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `question_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `question_delete_log` AFTER DELETE
    ON `j_pvoks_questions`
    FOR EACH ROW BEGIN
		SET @S = CONCAT(OLD.id,";", 
		OLD.category_id,";", 
		OLD.question_type,";", 
		OLD.title,";", 
		OLD.alias,";", 
		OLD.introtext,";", 
		OLD.fulltext,";", 
		OLD.state,";", 
		OLD.secret,";", 
		OLD.publicvote,";", 
		OLD.accredite_enabled,";", 
		OLD.target_category_id,";", 
		OLD.termins,";", 
		OLD.optvalid,";", 
		OLD.debatevalid,";", 
		OLD.votevalid,";", 
		OLD.created,";", 
		OLD.created_by,";", 
		OLD.modified,";", 
		OLD.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"question",OLD.id, OLD.created_by, "DELETE",@S);
    END$$
DELIMITER ;


/*categories */
DELIMITER $$
DROP TRIGGER IF EXISTS `category_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `category_insert_log` AFTER INSERT
    ON `j_pvoks_categories`
    FOR EACH ROW BEGIN
    	SET @S = CONCAT(NEW.id,";", 
	    NEW.parent_id,";", 
	    NEW.category_type,";", 
	    NEW.title,";", 
    	NEW.alias,";", 
    	NEW.introtext,";", 
    	NEW.fulltext,";", 
    	NEW.state,";", 
	    NEW.questvalid,";", 
	    NEW.created,";", 
    	NEW.created_by,";", 
    	NEW.modified,";", 
    	NEW.modified_by);
	  INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"category",NEW.id, NEW.created_by, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `category_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `category_update_log` AFTER UPDATE
    ON `j_pvoks_categories`
    FOR EACH ROW BEGIN
    	SET @S = CONCAT(NEW.id,";", 
	    NEW.parent_id,";", 
	    NEW.category_type,";", 
	    NEW.title,";", 
    	NEW.alias,";", 
    	NEW.introtext,";", 
    	NEW.fulltext,";", 
    	NEW.state,";", 
	    NEW.questvalid,";", 
	    NEW.created,";", 
    	NEW.created_by,";", 
    	NEW.modified,";", 
    	NEW.modified_by);
	INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"category",NEW.id, NEW.created_by, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `category_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `category_delete_log` AFTER DELETE
    ON `j_pvoks_categories`
    FOR EACH ROW BEGIN
    	SET @S = CONCAT(OLD.id,";", 
	    OLD.parent_id,";", 
	    OLD.category_type,";", 
	    OLD.title,";", 
    	OLD.alias,";", 
    	OLD.introtext,";", 
    	OLD.fulltext,";", 
    	OLD.state,";", 
	    OLD.questvalid,";", 
	    OLD.created,";", 
    	OLD.created_by,";", 
    	OLD.modified,";", 
    	OLD.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"category",OLD.id, OLD.created_by, "DELETE",@S);
    END$$
DELIMITER ;


/* options */
DELIMITER $$
DROP TRIGGER IF EXISTS `option_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `option_insert_log` AFTER INSERT
    ON `j_pvoks_options`
    FOR EACH ROW BEGIN
	    SET @S = CONCAT(NEW.id,";",  
        NEW.question_id,";",  
        NEW.title,";", 
        NEW.alias,";",  
        NEW.introtext,";",  
        NEW.fulltext,";",  
        NEW.state,";",  
        NEW.ordering,";",  
        NEW.created,";",  
        NEW.created_by,";",  
        NEW.modified,";",  
        NEW.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"option",NEW.id, NEW.created_by, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `option_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `option_update_log` AFTER UPDATE
    ON `j_pvoks_options`
    FOR EACH ROW BEGIN
	    SET @S = CONCAT(NEW.id,";",  
        NEW.question_id,";",  
        NEW.title,";", 
        NEW.alias,";",  
        NEW.introtext,";",  
        NEW.fulltext,";",  
        NEW.state,";",  
        NEW.ordering,";",  
        NEW.created,";",  
        NEW.created_by,";",  
        NEW.modified,";",  
        NEW.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"option",NEW.id, NEW.created_by, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `option_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `option_delete_log` AFTER DELETE
    ON `j_pvoks_options`
    FOR EACH ROW BEGIN
	    SET @S = CONCAT(OLD.id,";",  
        OLD.question_id,";",  
        OLD.title,";", 
        OLD.alias,";",  
        OLD.introtext,";",  
        OLD.fulltext,";",  
        OLD.state,";",  
        OLD.ordering,";",  
        OLD.created,";",  
        OLD.created_by,";",  
        OLD.modified,";",  
        OLD.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"option",OLD.id, OLD.created_by, "DELETE",@S);
    END$$
DELIMITER ;


/* members */
DELIMITER $$
DROP TRIGGER IF EXISTS `member_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `member_insert_log` AFTER INSERT
    ON `j_pvoks_members`
    FOR EACH ROW BEGIN
	  SET @S = CONCAT(NEW.id,";", 
	  NEW.category_id,";",  
	  NEW.user_id,";",  
	  NEW.state,";",  
	  NEW.admin,";",  
	  NEW.created,";",  
	  NEW.created_by,";",  
	  NEW.modified,";",  
	  NEW.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"member",NEW.id, NEW.created_by, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `member_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `member_update_log` AFTER UPDATE
    ON `j_pvoks_members`
    FOR EACH ROW BEGIN
	  SET @S = CONCAT(NEW.id,";", 
	  NEW.category_id,";",  
	  NEW.user_id,";",  
	  NEW.state,";",  
	  NEW.admin,";",  
	  NEW.created,";",  
	  NEW.created_by,";",  
	  NEW.modified,";",  
	  NEW.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"member",NEW.id, NEW.created_by, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `member_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `member_delete_log` AFTER DELETE
    ON `j_pvoks_members`
    FOR EACH ROW BEGIN
	  SET @S = CONCAT(OLD.id,";", 
	  OLD.category_id,";",  
	  OLD.user_id,";",  
	  OLD.state,";",  
	  OLD.admin,";",  
	  OLD.created,";",  
	  OLD.created_by,";",  
	  OLD.modified,";",  
	  OLD.modified_by);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"member",OLD.id, OLD.created_by, "DELETE",@S);
    END$$
DELIMITER ;


/* votes 
a titkos szavazás biztositása érdekében a vote rekornál az idõpontot nem naplozzuk. 
Lehetséges, hogy egyáltalánnem kellene naplozni? 
*/
DELIMITER $$
DROP TRIGGER IF EXISTS `vote_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `vote_insert_log` AFTER INSERT
    ON `j_pvoks_votes`
    FOR EACH ROW BEGIN
    SET @S = CONCAT(NEW.id,";", 
    NEW.question_id,";",  
    NEW.otion_id,";",  
    NEW.position,";",  
    NEW.anonym_voter,";",  
    NEW.voter_id);
      INSERT INTO `j_pvoks_logs` VALUES (0,0,"vote",NEW.id, NEW.anonym_voter, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `vote_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `vote_update_log` AFTER UPDATE
    ON `j_pvoks_votes`
    FOR EACH ROW BEGIN
    SET @S = CONCAT(NEW.id,";", 
    NEW.question_id,";",  
    NEW.otion_id,";",  
    NEW.position,";",  
    NEW.anonym_voter,";",  
    NEW.voter_id);
      INSERT INTO `j_pvoks_logs` VALUES (0,0,"vote",NEW.id, NEW.anonym_voter, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `vote_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `vote_delete_log` AFTER DELETE
    ON `j_pvoks_votes`
    FOR EACH ROW BEGIN
    SET @S = CONCAT(OLD.id,";", 
    OLD.question_id,";",  
    OLD.otion_id,";",  
    OLD.position,";",  
    OLD.anonym_voter,";",  
    OLD.voter_id);
     INSERT INTO `j_pvoks_logs` VALUES (0,0,"vote",OLD.id, OLD.anonym_voter, "DELETE",@S);
    END$$
DELIMITER ;


/* supports */
DELIMITER $$
DROP TRIGGER IF EXISTS `support_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `support_insert_log` AFTER INSERT
    ON `j_pvoks_supports`
    FOR EACH ROW BEGIN
	 SET @S = CONCAT(NEW.id,";", 
	 NEW.object_type,";",  
	 NEW.object_id,";",  
	 NEW.user_id,";",  
	 NEW.created);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"support",NEW.id, NEW.user_id, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `support_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `support_update_log` AFTER UPDATE
    ON `j_pvoks_supports`
    FOR EACH ROW BEGIN
	 SET @S = CONCAT(NEW.id,";", 
	 NEW.object_type,";",  
	 NEW.object_id,";",  
	 NEW.user_id,";",  
	 NEW.created);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"support",NEW.id, NEW.user_id, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `support_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `support_delete_log` AFTER DELETE
    ON `j_pvoks_supports`
    FOR EACH ROW BEGIN
	 SET @S = CONCAT(OLD.id,";", 
	 OLD.object_type,";",  
	 OLD.object_id,";",  
	 OLD.user_id,";",  
	 OLD.created);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"support",OLD.id, OLD.user_id, "DELETE",@S);
    END$$
DELIMITER ;


/* voters */
DELIMITER $$
DROP TRIGGER IF EXISTS `voter_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `voter_insert_log` AFTER INSERT
    ON `j_pvoks_voters`
    FOR EACH ROW BEGIN
 	  SET @S = CONCAT(NEW.id,";", 
	  NEW.question_id,";",  
	  NEW.user_id,";",  
	  NEW.created);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"voter",NEW.id, NEW.user_id, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `voter_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `voter_update_log` AFTER UPDATE
    ON `j_pvoks_voters`
    FOR EACH ROW BEGIN
 	  SET @S = CONCAT(NEW.id,";", 
	  NEW.question_id,";",  
	  NEW.user_id,";",  
	  NEW.created);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"voter",NEW.id, NEW.user_id, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `voter_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `voter_delete_log` AFTER DELETE
    ON `j_pvoks_voters`
    FOR EACH ROW BEGIN
 	  SET @S = CONCAT(OLD.id,";", 
	  OLD.question_id,";",  
	  OLD.user_id,";",  
	  OLD.created);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"voter",OLD.id, OLD.user_id, "DELETE",@S);
    END$$
DELIMITER ;


/* configs */
DELIMITER $$
DROP TRIGGER IF EXISTS `config_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `config_insert_log` AFTER INSERT
    ON `j_pvoks_configs`
    FOR EACH ROW BEGIN
 	  SET @S = CONCAT(NEW.id,";", 
	  NEW.config_type,";",  
	  NEW.title,";",  
	  NEW.json,";",  
	  NEW.created,";",  
	  NEW.created_by,";",  
	  NEW.modified,";",  
	  NEW.modified_by,";",  
	  NEW.state);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"config",NEW.id, NEW.created_by, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `config_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `config_update_log` AFTER UPDATE
    ON `j_pvoks_configs`
    FOR EACH ROW BEGIN
 	  SET @S = CONCAT(NEW.id,";", 
	  NEW.config_type,";",  
	  NEW.title,";",  
	  NEW.json,";",  
	  NEW.created,";",  
	  NEW.created_by,";",  
	  NEW.modified,";",  
	  NEW.modified_by,";",  
	  NEW.state);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"config",NEW.id, NEW.created_by, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `config_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `config_delete_log` AFTER DELETE
    ON `j_pvoks_configs`
    FOR EACH ROW BEGIN
 	  SET @S = CONCAT(OLD.id,";", 
	  OLD.config_type,";",  
	  OLD.title,";",  
	  OLD.json,";",  
	  OLD.created,";",  
	  OLD.created_by,";",  
	  OLD.modified,";",  
	  OLD.modified_by,";",  
	  OLD.state);
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"config",OLD.id, OLD.created_by, "DELETE",@S);
    END$$
DELIMITER ;


/* acrediteds */
DELIMITER $$
DROP TRIGGER IF EXISTS `acredited_insert_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `acredited_insert_log` AFTER INSERT
    ON `j_pvoks_acrediteds`
    FOR EACH ROW BEGIN
		SET @S = CONCAT(NEW.id,";", 
		NEW.category_id,";",  
		NEW.user_id,";",  
		NEW.acredited_id,";",  
		NEW.created, ";", 
		NEW.modified,";",  
		NEW.terminate,";"); 
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"acredited",NEW.id, NEW.user_id, "INSERT",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `acredited_update_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `acredited_update_log` AFTER UPDATE
    ON `j_pvoks_acrediteds`
    FOR EACH ROW BEGIN
		SET @S = CONCAT(NEW.id,";", 
		NEW.category_id,";",  
		NEW.user_id,";",  
		NEW.acredited_id,";",  
		NEW.created, ";", 
		NEW.modified,";",  
		NEW.terminate,";"); 
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"acredited",NEW.id, NEW.user_id, "UPDATE",@S);
    END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `acredited_delete_log`$$
CREATE
    DEFINER = CURRENT_USER 
    TRIGGER `acredited_delete_log` AFTER DELETE
    ON `j_pvoks_acrediteds`
    FOR EACH ROW BEGIN
		SET @S = CONCAT(OLD.id,";", 
		OLD.category_id,";",  
		OLD.user_id,";",  
		OLD.acredited_id,";",  
		OLD.created, ";", 
		OLD.modified,";",  
		OLD.terminate,";"); 
      INSERT INTO `j_pvoks_logs` VALUES (0,NOW(),"acredited",OLD.id, OLD.user_id, "DELETE",@S);
    END$$
DELIMITER ;







