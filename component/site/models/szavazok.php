<?php
 /**
  * szavazok model
  *   taskok: szavazok, szavazatEdit, szavazatDelete, eredmeny, szavazatSave
  * Licensz: GNU/GPL
  * Szerzõ: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  * JRequest: oevk, task
  */
defined('_JEXEC') or die;
  
class szavazokModel {
	private $errorMsg = '';
	function __construct() {
		$db = JFactory::getDBO();
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__szavazatok (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `temakor_id` int(11) NOT NULL COMMENT "témakör azonosító",
		  `szavazas_id` int(11) NOT NULL COMMENT "szavazás azonosító",
		  `szavazo_id` int(11) NOT NULL COMMENT "szavaó azonosító a concorde-shulze kiértékeléshez",
		  `user_id` int(11) NOT NULL COMMENT "Ha nyilt szavazás a szavazó user_id -je",
		  `alternativa_id` int(11) NOT NULL COMMENT "alternativa azonositó",
		  `pozicio` int(11) NOT NULL COMMENT "ebbe a pozicióba sorolta az adott alternativát",
		  PRIMARY KEY (`id`),
		  KEY `temakori` (`temakor_id`),
		  KEY `szavazasi` (`szavazas_id`),
		  KEY `useri` (`user_id`),
		  KEY `szavazoi` (`szavazo_id`)
		)');
		try {
			$db->query();
		} catch (Exception $e) {
			;
		}	
		
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__eredmeny (
			  `organization` int(11) NOT NULL DEFAULT 0 COMMENT "témakör ID",
			  `pollid` int(11) NOT NULL DEFAULT 0 COMMENT "szavazás ID",
			  `vote_count` int(11) NOT NULL DEFAULT 0 COMMENT "szavazatok száma",
			  `report` text COMMENT "cachelt report htm kód"
			)
		');
		try {
			$db->query();	
		} catch (Exception $e) {
			;
		}	
	}

    /**
    * get poll record from database
    * @param integer
    * @return object
    */
    public function getPollRecord($oevk) {
        $db = JFactory::getDBO();
		$db->setQuery('select * from #__categories where id='.$db->quote($oevk));
		$poll = $db->loadObject();
        return $poll;
    }
	
	/**
	  * egy adott oevk jelöltjeinek beolvasása
	  * @param integer oevk_id
	  * @return {"oevkId":szám, "oevkNev":string, "alternativak":[{"id":szám,"nev":string},....]}
	*/  
	public function getItem($szavazas_id) {
		$db = JFactory::getDBO();
		$result = new stdClass();
		$result->oevkId = $szavazas_id;
		$result->oevkNev = '';
		$result->alternativak = array();
		$db->setQuery('select * from #__categories where id='.$db->quote($szavazas_id));
		try {
		  $res = $db->loadObject(); 
		} catch (Exception $e) {
		  ;	
		}  
		if ($res) {
			$result->oevkNev = $res->title;
			$db->setQuery('select RAND(10) as rnd,
			if(substr(title,1,2)="--","zzzzzzzz",title) as sort,
			 c.*
			from #__content c
			where c.catid = '.$db->quote($szavazas_id).' and c.state=1 
			order by 2, c.title');
			try {
				$res = $db->loadObjectList();
			} catch (Exception $e) {
			  ;	
			}  
			foreach ($res as $res1) {
				$w = new stdClass();
				$w->id = $res1->id;
				$w->nev = $res1->title;
				$w->introtext = $res1->introtext;
				$result->alternativak[] = $w;
			}
		}
		return $result;
	}
	
	/**
	  * get OEVK ID jelölt ID alapján
	  * @param integer jelölt ID
	  * @return integer oevk ID
	  */
	public function getOevkFromJelolt($jeloltId,$config) {
		$db = JFactory::getDBO();
		$result = 0;
		$db->setQuery('select * from #__content where id='.$db->quote($jeloltId));
		try {
			$res = $db->loadObject();
		} catch (Exception $e) {
			;
		}	
		if ($res) {
			if (($res->catid >= $config->oevk_min) & ($res->catid <= $config->oevk_max))
			    $result = $res->catid;
		}
		return $result;
	}
	
	public function getErrorMsg() {
	  return $this->errorMsg;	
	}
	
	/**
	* szavazat tárolása adatbázisba - oevk szavazásnál előtörléssel
	* @param integer oevk id
	* @param string jelolt_id=pozicio, jelolt_id=pozicio, ....
	* @param JUser
	* @return integer szavazo azonositó, ha hiba lépett fel akkor 0
	*/  
	public function save($szavazas_id, $szavazat, $user) {
		global $evConfig;
        $szavazoId = (rand(100,999).$user->id)*2;
		$msg = '';

		// jogosultság ellenörzés
		if (teheti($szavazas_id, $user, 'szavazas', $msg) == false) {
			  $this->errorMsg .= $msg;
			  return 0;	
		}

		$db = JFactory::getDBO();
		$db->setQuery('START TRANSACTION');
		$db->query();

		// szavazás kategoria megállapitása
		$db->setQuery('select * from #__categories where id='.$db->quote($szavazas_id));
		$res = $db->loadObject();
		if ($res)
			$catid = $res->parent_id;
		else
			$catid = 0;
		if ($szavazoId > 0) {
			// string részekre bontása és tárolás ciklusban
			$w1 = explode(',',$szavazat);
			foreach ($w1 as $item) {
				$w2 = explode('=',$item);
				$db->setQuery('INSERT INTO #__szavazatok 
					(`temakor_id`, 
					`szavazas_id`, 
					`szavazo_id`, 
					`user_id`, 
					`alternativa_id`, 
					`pozicio`
					)
					VALUES
					('.$db->quote($catid).', 
					'.$db->quote($szavazas_id).', 
					'.$db->quote($szavazoId).', 
					'.$db->quote($user->id).', 
					'.$db->quote($w2[0]).', 
					'.$db->quote($w2[1]).'
					)
				');
				try {
				  if ($db->query() != true) {
					$this->errorMsg .= $db->getErrorMsg().'<br />';
					$szavazoId = 0;
				  }
				} catch (Exception $e) {
					$szavazoId = 0;
				}	
			}
		}

		// delete cached report
		$db->setQuery('UPDATE #__eredmeny 
		SET report="" 
		WHERE pollid='.$db->quote($szavazas_id));
		try {
		  $db->query();
		} catch (Exception $e) {
		  ;
		}	

		if ($szavazoId > 0) 
			$db->setQuery('COMMIT');
		else
			$db->setQuery('ROLLBACK');
		$db->query();

		return $szavazoId;
	}	

    /**
    * get report from cache
    * @param integer
    * @return object
    */
    public function getFromCache($oevk) {
        $db = JFactory::getDBO();
		$db->setQuery('select * from 
					 #__eredmeny 
					 where pollid='.$db->quote($oevk));
		$cache = $db->loadObject();
        return $cache;
    }

    /**
    * init report cache
    * @param integer
    * @return object
    */
    public function initCache($oevk) {
        $db = JFactory::getDBO();
		$db->setQuery('INSERT INTO #__eredmeny
		(pollid, report) 
		value 
		('.$db->quote($oevk).',"")');
		$db->query();
    }

    /**
    * save report to cache
    * @param integer
    * @param integer
    * @param string
    * @return object
    */
    public function saveToCache($oevk, $vote_count, $report) {
        $db = JFactory::getDBO();
		$db->setQuery('update #__eredmeny 
		set report='.$db->quote($report).',
			vote_count = '.$db->quote($vote_count).'
		where pollid='.$db->quote($oevk));
		$db->query();
    }

    /**
    * get szavazatok lista az adatbázisból
    * @param integer
    * @return array of object
    */
    public function getSzavazatok($oevk) {
		$db = JFactory::getDBO();
		$db->setQuery('select sz.szavazas_id, sz.szavazo_id, sz.pozicio, c2.title altTitle,
		c1.title szTitle
		from #__szavazatok sz
		left outer join #__content c2 on c2.id = sz.alternativa_id
		left outer join #__categories c1 on c1.id = sz.szavazas_id
		where c2.state = 1 and 
		sz.szavazas_id = '.$db->quote($oevk).'
		order by 1,2,3
		');
		$res = $db->loadObjectList();
        return $res;
    }

}	
?>
