<?php
 /**
  * javaslatok model
  * Licensz: GNU/GPL
  * Szerzõ: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  * JRequest: oevk, task
  */
defined('_JEXEC') or die;

  
class JavaslatokModel {
	
	private $errorMsg = '';

	function __construct() {
		$db = JFactory::getDBO();
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__supports (
		  `id` INT(11) NOT NULL AUTO_INCREMENT,
		  `proposal_id` INT(11) NOT NULL COMMENT "javaslat azonosító",
		  `user_id` INT(11) NOT NULL COMMENT "user azonosító",
		  PRIMARY KEY (`id`),
		  KEY `proposali` (`proposal_id`),
		  KEY `useri` (`user_id`))
		)');
		try {
			$db->query();
		} catch (Exception $e) {
			return;
		}	
	}

    /**
    * get proposals list record from database
    * @param integet category_id
    * @param JUser logged user object
    * @return array of object
    */
    public function getJavaslatok($categoryId, $user) {
      $db = JFactory::getDBO();
		$db->setQuery('select j.id, j.title, j.introtext, 
		count(s.id) tamogatottsag, 
		sum(if(s.user_id = '.$user->id.',1,0)) tamogatod 
		from #__content j
		left outer join #__supports s on s.proposal_id = j.id
		where j.catid = '.$db->quote($categoryId).' and `state`=1
		group by j.id, j.title, j.introtext
		order by 4 DESC, 2 ASC
		');
		$result = $db->loadObjectList();
		$this->errorMsg = $db->getErrorMsg();
		return $result;
    }
	
	
	public function getErrorMsg() {
	  return $this->errorMsg;	
	}

	/**
	* javaslat tárolása
	* @return string errorMsg or ''
	*/ 
	public function javaslatsave($evConfig, 
		$nev, $eletrajz, $program, $tamogatok, $kontakt, $kepURL) {
			
		function createArticle($data) {
		    $user = JFactory::getUser();
		    $db = JFactory::getDBO();
		    $db->setQuery('insert into #__content 
		    (`id`, `catid`, `alias`, `title`, `introtext`, 
		    `fulltext`, `state`, `language`, `access`, `created_by`, `created_by_alias`)
		    values 
		    (0,'.$db->quote($data['catid']).',
		    '.$db->quote($data['alias']).',
			 '.$db->quote($data['title']).',
			 '.$db->quote($data['introtext']).',
			 '.$db->quote($data['fulltext']).',
			 '.$db->quote($data['state']).',
			 '.$db->quote($data['language']).',
			 '.$db->quote($data['access']).',
			 '.$db->quote($user->id).',
			 '.$db->quote($user->username).'
			 )');
		    if ($db->query()) {
				  $db->setQuery('select max(id) id from #__content');
				  $res = $db->loadObject();	
		        return $res->id;
		    } else {
		        $err_msg = $db->getErrorMsg();
		        return false;
		    }
		    
		}
		
		global $evConfig;
		$result = '';	
		$introText = '<img style="float:left; width:250px;" src="'.$kepURL.'" />'. 
		'<h3>Program</h3>'.
		echoHtmlDiv(substr($program,0,400),'programIntro').
		echoHtmlDiv($tamogatok,'tamogatok');
		
		$fullText =
		echoHtmlDiv(substr($program,401,20000),'programFull').
		echoHtmlDiv($eletrajz,'eletrajz').
  		'<h3>Támogató szervezetek</h3>'.
		echoHtmlDiv($tamogatok,'tamogatoSzervezetekFull').
  		'<h4>Kontakt infó PUBLIKÁLÁS ELŐTT TÖRLENDŐ!!!!</h4>'.
		echoHtmlDiv($kontakt,'kontakt');
		$article_data = array(
		    'id' => 0,
		    'catid' => $evConfig->pollDefs[$evConfig->pollId]->proposals,
		    'title' => $nev,
		    'alias' => '',
		    'introtext' => $introText,
		    'fulltext' => $fullText,
		    'state' => 0, //if you want to keep the article published else 0
		    'language' => '*',
		    'access' => 1
		);
		$article_id = createArticle($article_data);
		if ($article_id === false) {
		    $result = "Article create failed!";
		} else {
		    $result = '';
		}
		return $result;
	} // javaslatSave
	
    /**
    * get proposal record from database
    * @param integet id
    * @param JUser logged user object
    * @return array of object
    */
	public function getJavaslat($id,$user) {
      $db = JFactory::getDBO();
		$db->setQuery('select j.id, j.title, j.introtext, j.fulltext, 
		count(s.id) tamogatottsag, 
		sum(if(s.user_id = '.$user->id.',1,0)) tamogatod 
		from #__content j
		left outer join #__supports s on s.proposal_id = j.id
		where j.id = '.$db->quote($id).'
		group by j.id, j.title, j.introtext, j.fulltext
		order by 3,1
		');
		$result = $db->loadObject();
		$this->errorMsg = $db->getErrorMsg();
		return $result;
	}
	
    /**
    * set proposal support into database
    * @param integet id
    * @param JUser logged user object
    * @param bool support / unsupport
    * @return true: elérte a szükséges támogatottságot, false: nem
    */
	public function tamogatom($id,$user, $mode) {
		if ($user->id > 0) {
	      $db = JFactory::getDBO();
	      $db->setQuery('delete from #__supports 
	      where proposal_id='.$db->quote($id).' and user_id='.$db->quote($user->id));
			$db->query();
			if ($mode) {
	      	$db->setQuery('insert into #__supports values 
	      	(0,'.$db->quote($id).','.$db->quote($user->id).')');
				$db->query();
				$result = $this->checkSupportCount($id);
			}	      
		}
		return $result;
	}
	
	/**
	* check supportCount > $evConfig->pollDefs[$id]->requestedSupport ?
	* if true then move proposal --> candidate
	* @param integer proposalId
	* @return boolean succes or not?
	*/
	protected function checkSupportCount($id) {
	  global $evConfig;
	  $result = true;
	  $pollId = $evConfig->pollId;
	  if ($evConfig->pollDefs[$pollId]->requestedSupport > 0) {
    	  $db = JFactory::getDBO();
          $db->setQuery('select count(user_id) cc 
          from #__supports
          where proposal_id = '.$db->quote($id));
          $res = $db->loadObject();
          if ($res->cc >= $evConfig->pollDefs[$pollId]->requestedSupport) {
    			$db->setQuery('update #__content
    			set catid = '.$db->quote($pollId).'
    			where id = '.$db->quote($id));
    			if (!$db->query()) {
    				echo '<div class="alert alert-danger">Hiba lépett fel a javaslat jelölté modosítása közben</div>'; 
    				exit();			
    			} else {
    				$result = true;
    			}     
          }
	  }
      return $result;
	}
	
	/**
	 * támogatottság jelölté válásának feltétele
	 * @param integer $proposalCatId
	 * @param integer $requeredCandidatesCount
	 * @return number
	 */
	protected function getSupportLimit($proposalCatId, $requeredCandidatesCount) {
        global $evConfig;
        $db = JFactory::getDBO();
        $pollId = $evConfig->pollId;
	    $supportX = $evConfig->pollDefs[$pollId]->requestedSupport;
	    if ($supportX == 0) {
    	    $db->setQuery('SELECT s.proposal_id, COUNT(s.user_id) cc
                              FROM #__supports s
                              INNER JOIN #__content c ON s.proposal_id = c.id
                              WHERE c.catid = '.$db->quote($proposalCatId).' AND c.state = 1
                              GROUP BY s.proposal_id
                              ORDER BY 2 DESC;
               ');
    	    $res = $db->loadObjectList();
            if ($db->getErrorNum() != 0) {
                $this->errorMsg = $db->getErrorMsg();
            }
    	    if ($db->getErrorNum() == 0) {
    	        // konfig szerinti pozición lévő támogatottság
    	        if ($requeredCandidatesCount < count($res)) {
    	            $supportX = $res[$requeredCandidatesCount]->cc;
    	        } else {
    	            $supportX = 0;
    	        }
    	    }
	    }
	    return $supportX;
	}
	
	/**
	 * create workTable for supportEnd
	 * @param string $workTableName
	 * @param integer $proposalCatId
	 * @param integer $supportX
	 * @return boolean success or not?
	 */
	protected function createSupportEndWorkTable($workTableName, $proposalCatId, $supportX) {
	    $db = JFactory::getDBO();
	    // munkatábla létrehozása
	    $db->setQuery('CREATE TABLE `'.$workTableName.'`
                              SELECT s.proposal_id, COUNT(s.user_id) cc
                              FROM  #__supports s
                              INNER JOIN #__content c ON s.proposal_id = c.id
                              WHERE c.catid = '.$db->quote($proposalCatId).' AND c.state = 1
                              GROUP BY s.proposal_id
                              HAVING COUNT(s.user_id) >= '.$db->quote($supportX).'
                              ORDER BY 2 DESC;
               ');
	    $db->query();
        if ($db->getErrorNum() != 0) {
            $this->errorMsg = $db->getErrorMsg();
        }
	    return ($db->getErrorNum() == 0);
	}
	
	/**
	 * support time end, proposal --> candides
	 * @param integer $pollId
	 * @param integer $proposalCatId
	 * @param integer $requeredCandidatesCount
	 * @return boolean success or not?
	 */
	public function supportEnd($pollId, $proposalCatId, $requeredCandidatesCount) {
	    $db = JFactory::getDBO();
	    $result = true;
	    $workTableName = '#__supportWork'.$pollId;
	    $this->errorMsg = '';
	    
	    // ha már van munkatábla, akkor már futoott, újre ne fusson
	    $db->setQuery('SHOW TABLES LIKE '.$db->quote($workTableName));
	    $res = $db->loadObjectList();
	    if (count($res) == 0) {
	       // javaslat jelölté válásának feltételének lekérése
	       $supportX = $this->getSupportLimit($proposalCatId, $requeredCandidatesCount);
	       
	       if (($this->errorMsg == '') && ($this->createSupportEndWorkTable($workTableName, $proposalCatId, $supportX))) {
	               // proposal --> candidate modosítás
	               $db->setQuery('UPDATE #__content c, `'.$workTableName.'` w
                              SET catid = '.$db->quote($pollId).'
                              WHERE c.id = w.proposal_id;
                   ');
	               $db->query();
	               if ($db->getErrorNum() != 0) {
    	               $this->errorMsg = $db->getErrorMsg();
	               }
	       }
	       if ($this->errorMsg == '') {
	           $result = true;
	       } else {
	           $result = false;
	       }
	    } else {
	        $result = false;
	        $this->errorMsg = $workTableName.' worktable exists';
	    }
	    return $result;
	} // supportEnd
	
}	// javaslatokModel
?>
