<?php
 /**
  * szavazok model
  * taskok: szavazok, eredmeny, szavazatSave
  * Licensz: GNU/GPL
  * Szerzõ: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  * JRequest: oevk, task
  */
defined('_JEXEC') or die;

class MyCondorcet extends Condorcet {
	
		public $szavazatokTable = '#__szavazatok'; // szükség esetén átirányitható egy view -re

      protected function getCandidates() {
          $db = JFactory::getDBO();
          $candidates_sql = 'select title AS megnevezes,id
		      from #__content
		      where  catid='.$db->quote($this->poll).' and state=1';
          $db->setQuery($candidates_sql);
          $this->candidates=array();
		 	 $this->condorcetGyoztes = array();
          foreach($db->loadObjectList() as $row) {
              $this->candidates[$row->id] = $row->megnevezes;
          }
          return $this->candidates;
      }

      protected function loadDiffMatrix() {
          $db = JFactory::getDBO();
          $diff_sql = 'select a.alternativa_id as id1, b.alternativa_id as id2, count(a.id) as d
                       from '.$this->szavazatokTable.' a,
                            '.$this->szavazatokTable.' b
                       where  a.szavazas_id='.$db->quote($this->poll).' and
                             b.szavazas_id=a.szavazas_id and
                             a.szavazo_id=b.szavazo_id and
                             a.pozicio < b.pozicio 
                       group by a.alternativa_id, b.alternativa_id';
          $db->setQuery($diff_sql);
          $this->dMatrix=array();
		 	 $rows = $db->loadObjectList();
          foreach($rows as $row ) {
              $id1 = $row->id1;
              $id2 = $row->id2;
              $d = $row->d;
              if(!array_key_exists($id1,$this->dMatrix)) {
                  $this->dMatrix[$id1] = array();
              }
              $this->dMatrix[$id1][$id2] = $d;
          }
          foreach($this->candidates as $id1 => $name1) {
              if(!array_key_exists($id1,$this->dMatrix)) {
                  $this->dMatrix[$id1] = array();
              }
              foreach($this->candidates as $id2 => $name2) {
                  if(!array_key_exists($id2,$this->dMatrix[$id1])) {
                      $this->dMatrix[$id1][$id2] = 0;
                  }
              }
          }
          return $this->dMatrix;
      }

      protected function loadInFirst() {
        $db = JFactory::getDBO();
        foreach($this->candidates as $id1 => $name1) {
            $this->inFirst[$id1] = 0;        
        }
        $db->setQuery('select a.alternativa_id, count(a.szavazo_id) cc
        from '.$this->szavazatokTable.' a
        where a.szavazas_id = '.$db->quote($this->poll).' and a.pozicio = 1 
        group by a.alternativa_id
        ');
        $res = $db->loadObjectList();
        foreach ($res as $row) {
            $this->inFirst[$row->alternativa_id] = $row->cc;
        }
        return $this->inFirst;
      }  

      protected function loadVoteCount() {  
        $db = JFactory::getDBO();
   	    $db->setQuery('select count(DISTINCT a.szavazo_id) cc
 		from '.$this->szavazatokTable.' a
		left outer join #__content c2 on c2.id = a.alternativa_id
    	where c2.state = 1 and a.szavazas_id = '.$db->quote($this->poll));		
		$res = $db->loadObject();
   	    $this->vote_count = $res->cc;
        return $this->vote_count;
      }  
} // myCondorcet
  
class SzavazokModel {
	
	public $szavazatokTable = '#__szavazatok'; // szükség esetén átirányitható egy view -re

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
			return;
		}	
		
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__eredmeny (
			  `organization` int(11) NOT NULL DEFAULT 0 COMMENT "témakör ID",
			  `pollid` int(11) NOT NULL DEFAULT 0 COMMENT "szavazás ID",
			  `vote_count` int(11) NOT NULL DEFAULT 0 COMMENT "szavazatok száma",
			  `report` text COMMENT "cachelt report htm kód",
			  `table` varchar(128) NOT NULL DEFAULT 0 COMMENT "szavazatok tábla vagy view neve"
			)
		');
		try {
			$db->query();	
		} catch (Exception $e) {
			return;
		}	
	}

    /**
    * get poll record from database
    * @param integer
    * @return object
    */
    public function getPollRecord($pollId) {
      $db = JFactory::getDBO();
		$db->setQuery('select * from #__categories where id='.$db->quote($pollId));
		return $db->loadObject();
    }
	
	/**
	  * egy adott poll jelöltjeinek beolvasása
	  * @param integer pollId
	  * @return {"pollId":szám, "pollName":string, "pollOptions":[{"id":szám,"name":string},....]}
	*/  
	public function getItem($pollId) {
		$db = JFactory::getDBO();
		$result = new stdClass();
		$result->pollId = $pollId;
		$result->pollNamev = '';
		$result->pollOptions = array();
		$res = $this->getPollRecord($pollId);
		if ($res) {
			$result->pollName = $res->title;
			$db->setQuery('select RAND(10) as rnd,
			if(substr(title,1,2)="--","zzzzzzzz",title) as sort,
			 c.*
			from #__content c
			where c.catid = '.$db->quote($pollId).' and c.state=1 
			order by 2, c.title');
			try {
				$res = $db->loadObjectList();
			} catch (Exception $e) {
			   $res = [];	
			}  
			foreach ($res as $res1) {
				$w = new stdClass();
				$w->id = $res1->id;
				$w->name = $res1->title;
				$w->introtext = $res1->introtext;
				$result->pollOptions[] = $w;
			}
		}
		return $result;
	}
	
	public function getErrorMsg() {
	  return $this->errorMsg;	
	}
	
	/**
	* szavazat tárolása adatbázisba 
	* @param integer pollId
	* @param string jelolt_id=pozicio, jelolt_id=pozicio, ....
	* @param JUser
	* @return integer szavazo azonositó, ha hiba lépett fel akkor 0
	*/  
	public function save($pollId, $szavazat, $user) {
		global $evConfig;
      $szavazoId = (rand(100,999).$user->id)*2;
		$msg = '';
		$SEPARATOR=',';
      if (!$evConfig->pollDefs[$pollId]->votingEnable) {
			  $this->errorMsg .= 'Most nem lehet szavazni.';
			  return 0;	
      }

		// jogosultság ellenörzés
		if (!teheti($pollId, $user, 'szavazas', $msg)) {
			  $this->errorMsg .= $msg;
			  return 0;	
		}

		$db = JFactory::getDBO();
		$db->setQuery('START TRANSACTION');
		$db->query();

		// szavazás kategoria megállapitása
		$res = $this->getPollRecord($pollId);
		if ($res) {
			$catid = $res->parent_id;
		} else {
			$catid = 0;
		}	
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
					('.$db->quote($catid).$SEPARATOR. 
					$db->quote($pollId).$SEPARATOR. 
					$db->quote($szavazoId).$SEPARATOR. 
					$db->quote($user->id).$SEPARATOR. 
					$db->quote($w2[0]).$SEPARATOR. 
					$db->quote($w2[1]).')');
				try {
				  if (!$db->query()) {
					$this->errorMsg .= $db->getErrorMsg().'<br />';
					$szavazoId = 0;
				  }
				} catch (Exception $e) {
					$szavazoId = 0;
				}	
			}
		}

		// save report to cache
		$db->setQuery('UPDATE #__eredmeny 
		SET report="" 
		WHERE pollid='.$db->quote($pollId));
	   $db->query();

		if ($szavazoId > 0) {
			$db->setQuery('COMMIT');
		} else {
			$db->setQuery('ROLLBACK');
		}	
		$db->query();

		return $szavazoId;
	}	

    /**
    * get report from cache
    * @param integer
    * @return object
    */
    public function getFromCache($pollId) {
      $db = JFactory::getDBO();
		$db->setQuery('select * from 
					 #__eredmeny 
					 where pollid='.$db->quote($pollId).' and `table`='.$db->quote($this->szavazatokTable));
		return $db->loadObject();
    }

    /**
    * init report cache
    * @param integer
    * @return object
    */
    public function initCache($pollId) {
      $db = JFactory::getDBO();
		$db->setQuery('INSERT INTO #__eredmeny
		(pollid, report) 
		value 
		('.$db->quote($pollId).',"")');
		$db->query();
    }

    /**
    * save report to cache
    * @param integer
    * @param integer
    * @param string
    * @return object
    */
    public function saveToCache($pollId, $vote_count, $report) {
      $db = JFactory::getDBO();
		$db->setQuery('update #__eredmeny 
		set report='.$db->quote($report).',
 			 vote_count = '.$db->quote($vote_count).',
			 `table` = '.$db->quote($this->szavazatokTable).'
		where pollid='.$db->quote($pollId));
		$db->query();
    }

    /**
    * get szavazatok lista az adatbázisból
    * @param integer
    * @return array of object
    */
    public function getSzavazatok($pollId) {
		$db = JFactory::getDBO();
		$db->setQuery('select sz.szavazas_id, sz.szavazo_id, sz.pozicio, c2.title altTitle,
		c1.title szTitle
		from '.$this->szavazatokTable.' sz
		left outer join #__content c2 on c2.id = sz.alternativa_id
		left outer join #__categories c1 on c1.id = sz.szavazas_id
		where c2.state = 1 and 
		sz.szavazas_id = '.$db->quote($pollId).'
		order by 1,2,3
		');
		return $db->loadObjectList();
    }

}	// szavazokModel
?>
