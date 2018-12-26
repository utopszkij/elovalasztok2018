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
	*/
	protected function checkSupportCount($id) {
		global $evConfig;
		$result = true;
		$pollId = $evConfig->pollId;
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
      return $result;
	}
}	// javaslatokModel
?>
