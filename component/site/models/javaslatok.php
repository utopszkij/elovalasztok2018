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
		$this->errorMsg = $db->error();
		//echo JSON_encode($result);		
		return $result;
    }
	
	
	public function getErrorMsg() {
	  return $this->errorMsg;	
	}

	/**
	* javaslat tárolása
	* @return string errormsg
	*/ 
	public function javaslatsave($evConfig, 
		$nev, $eletrajz, $program, $tamogatok, $kontakt, $kepURL) {
		function createArticle($data) {
		    $data['rules'] = array(
		        'core.edit.delete' => array(),
		        'core.edit.edit' => array(),
		        'core.edit.state' => array()
		    );
		
		    $basePath = JPATH_ADMINISTRATOR.'/components/com_content';
		    require_once $basePath.'/models/article.php';
		    $article_model =  JModelLegacy::getInstance('Article','ContentModel');
		    // or  $config= array(); $article_model =  new ContentModelArticle($config);
		    if (!$article_model->save($data)) {
		        $err_msg = $article_model->getError();
		        return false;
		    } else {
		        $id = $article_model->getItem()->id;
		        return $id;
		    }
		}
		$result = '';	
		$introText = '<img style="float:left; width:250px;" src="'.$kepURL.'" />'. 
		'<h3>Program</h3><div class="programIntro">'.substr($program,0,400).'</div>'.
  		'<div class="tamogatoszervezetekIntro">'.$tamogatok.'</div>';
		
		$fullText = '<div class="programFull">'.substr($program,401,20000).'</div><h3>Életrajz</h3>'.
  		'<div class="eletrajz">'.$eletrajz.'</div>'.
  		'<h3>Támogató szervezetek</h3><div class="tamogatoszervezetekFull">'.$tamogatok.'</div>'.
  		'<h4>Kontakt infó PUBLIKÁLÁS ELŐTT TÖRLENDŐ!!!!</h4><div class="kontakt">'.$kontakt.'</div>';
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
		if (!$article_id) {
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
		$this->errorMsg = $db->error();
		// echo $db->getQuery();		
		return $result;
	}
	
    /**
    * set proposal support into database
    * @param integet id
    * @param JUser logged user object
    * @param bool support / unsupport
    * @return array of object
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
			}	      
		}
	}
}	// szavazokModel
?>
