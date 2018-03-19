DROP VIEW IF EXISTS j_szavazatok_email; 
CREATE  VIEW j_szavazatok_email 
    AS (
	SELECT sz.*
	FROM j_szavazatok sz
	INNER JOIN j_user_usergroup_map um ON um.user_id = sz.szavazo_id
	INNER JOIN j_usergroups ug ON ug.id = um.group_id
	WHERE ug.title = "email"
    );
    
DROP VIEW IF EXISTS j_szavazatok_magyar; 
CREATE  VIEW j_szavazatok_magyar 
    AS (
	SELECT sz.*
	FROM j_szavazatok sz
	INNER JOIN j_user_usergroup_map um ON um.user_id = sz.szavazo_id
	INNER JOIN j_usergroups ug ON ug.id = um.group_id
	WHERE ug.title = "magyar"
    );
    
DROP VIEW IF EXISTS j_szavazatok_hashgiven; 
CREATE  VIEW j_szavazatok_hashgiven 
    AS (
	SELECT sz.*
	FROM j_szavazatok sz
	INNER JOIN j_user_usergroup_map um ON um.user_id = sz.szavazo_id
	INNER JOIN j_usergroups ug ON ug.id = um.group_id
	WHERE ug.title = "hashgiven"
    );
    
    
