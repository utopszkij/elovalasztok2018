<?php
/**
  * szavazok component
  *   taskok: szavazok, szavazatedit, szavazatDelete, eredmeny, szavazatSave
  * Licensz: GNU/GPL
  * Szerző: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  * JRequest: pollId, task
  */
  defined('_JEXEC') or die;
  global $evConfig;
  jimport('joomla.application.component.controller');
  jimport('joomla.application.component.model');
  jimport('joomla.application.component.view');
  jimport('joomla.application.component.helper');

  include_once dirname(__FILE__).'/config.php';
  include_once dirname(__FILE__).'/accesscontrol.php';
  include_once dirname(__FILE__).'/funkciok.php';
  include_once dirname(__FILE__).'/condorcet.php';
  include_once dirname(__FILE__).'/models/szavazok.php';
  include_once dirname(__FILE__).'/models/javaslatok.php';
  
  define('POLIIID','pollId');
  define('COOKIE_ENABLE','cookie_enable');
  define('COOKIE_MSG','Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!');
  define('STRING','string');
  define('BUDAPESTI','ADA:magyar,budapest');
  define('MYCSRTOKEN','myCsrToken');
  define('INVALIDCSRTOKEN','Invalid CSR token');
  define('NOTASSURANCE','Nincs megfelelő tanusítása');
  define('JAVASLATOKURL','component/elovalasztok?task=javaslatok');
  define('ERRORMSG','errorMsg');
  define('INFOMSG','infoMsg');

  
  $user = JFactory::getUser();
  $msg = '';
  $input = JFactory::getApplication()->input;
  $session = JFactory::getSession();  
  $pollId = $input->get(POLLID,$evConfig->pollId,0);   // szavazás ID (category ID)
  if ($pollId == 0) {
	  $pollId = $input->get('id',0);
  }
  if ($pollId == 0) {
	  $pollId = $session->get(POLLID,$evConfig->pollId);
  }
  $session->set(POLLID,$pollId);
  $evConfig->pollId = $pollId;

  $task = $input->get('task','szavazok');

  // ================ controller ==============================
  class SzavazoController extends JcontrollerLegacy {
	
	/**
	* if testMode then display test info msg
	*/
	protected function displayTestMsg($evConfig, $pollId) {
		if ($evConfig->pollDefs[$pollId]->testMode) {
			echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
		}
	}	
	  
	/**
	* display szavazo Form
	* @param int $pollId
	* @param Jmodel
	*/  
	protected function displaySzavazoForm($pollId, $model) {	  		
	   $item = $model->getItem($pollId);	
		if (count($item->pollOptions) <= 0) {
		    echo echoHtmlDiv('Nincs jelölt', INFOMSG);
		} else {
            include dirname(__FILE__).'/views/szavazoform.php';
		}  
	}	

	/**
	* check Cookie Enabled, if not enabled exit program
	*/
	private function cookieCheck() {
			$session = JFactory::getSession();
			if ($session->get(COOKIE_ENABLE,'0') != 1) {
				echo echoHtmlDiv(COOKIE_MSG,ERRORMSG);
				exit();
			}
	}

	/**
   * szavazó képernyő megjelenitése  - új szavazat beküldése
	* @param integer szavazás azonosító
   * @param JUser user 
   */ 	
    public function szavazok($pollId, $user) {
			global $evConfig;
			$this->cookieCheck();
			$model = new SzavazokModel();
			$this->displayTestMsg($evConfig, $pollId);	
			if ($evConfig->pollDefs[$pollId]->testMode) {
				$this->displaySzavazoForm($pollId, $model);
				return;
			}		
			$msg = '';
			$infoMsg = INFOMSG;
			if ($user->id == 0) {
					echo echoHtmlDiv('Szavazáshoz be kell jelentkezni!','errorMsg notLogin');
			} else if (!$evConfig->pollDefs[$pollId]->votingEnable) {
					echo echoHtmlDiv('Jelenleg nem lehet szavazni.',$infoMsg);
			} else  {
			  if (teheti($pollId, $user, 'szavazas', $msg)) {
			  		$this->displaySzavazoForm($pollId, $model);
			  } else {
					echo echoHtmlDiv($msg,ERRORMSG);
			  }
			}
	}
	
    /**
     * check table ot view exists?
     *
     * @param string $table
     * @return bool
     */
    protected function checkView($table)
    {
        $db = JFactory::getDBO();
        $db->setQuery('SHOW TABLES LIKE '.$db->quote($table));
        $res = $db->loadObjectList();
        return (count($res) > 0); 
    }

	protected function createView($table)
	{
        $db = JFactory::getDBO();
        if ($table == '#__appmagyar') {
            $db->setQuery('
            CREATE VIEW #__appmagyar AS (
            SELECT
              `sz`.`id`             AS `id`,
              `sz`.`temakor_id`     AS `temakor_id`,
              `sz`.`szavazas_id`    AS `szavazas_id`,
              `sz`.`szavazo_id`     AS `szavazo_id`,
              `sz`.`user_id`        AS `user_id`,
              `sz`.`alternativa_id` AS `alternativa_id`,
              `sz`.`pozicio`        AS `pozicio`
            FROM #__szavazatok sz
            LEFT OUTER JOIN #__users u ON u.id = sz.user_id
            WHERE (u.params LIKE "%appmagyar%") AND (u.params LIKE "%budapest%")
            )
            ');
        } else if ($table == '#__offline') {
            $db->setQuery('
            CREATE VIEW #__offline AS (
            SELECT
              `sz`.`id`             AS `id`,
              `sz`.`temakor_id`     AS `temakor_id`,
              `sz`.`szavazas_id`    AS `szavazas_id`,
              `sz`.`szavazo_id`     AS `szavazo_id`,
              `sz`.`user_id`        AS `user_id`,
              `sz`.`alternativa_id` AS `alternativa_id`,
              `sz`.`pozicio`        AS `pozicio`
            FROM #__szavazatok sz
            LEFT OUTER JOIN #__users u ON u.id = sz.user_id
            WHERE (u.params LIKE "%offline%") AND (u.params LIKE "%budapest%")
            )
            ');
        } else if ($table == '#__hiteles') {
            $db->setQuery('
            CREATE VIEW #__hiteles AS (
            SELECT
              `sz`.`id`             AS `id`,
              `sz`.`temakor_id`     AS `temakor_id`,
              `sz`.`szavazas_id`    AS `szavazas_id`,
              `sz`.`szavazo_id`     AS `szavazo_id`,
              `sz`.`user_id`        AS `user_id`,
              `sz`.`alternativa_id` AS `alternativa_id`,
              `sz`.`pozicio`        AS `pozicio`
            FROM #__szavazatok sz
            LEFT OUTER JOIN #__users u ON u.id = sz.user_id
            WHERE (u.params LIKE "%budapest%")
            )
            ');
        } else if ($table == '#__magyar') {
            $db->setQuery('
            CREATE VIEW #__magyar AS (
            SELECT
              `sz`.`id`             AS `id`,
              `sz`.`temakor_id`     AS `temakor_id`,
              `sz`.`szavazas_id`    AS `szavazas_id`,
              `sz`.`szavazo_id`     AS `szavazo_id`,
              `sz`.`user_id`        AS `user_id`,
              `sz`.`alternativa_id` AS `alternativa_id`,
              `sz`.`pozicio`        AS `pozicio`
            FROM #__szavazatok sz
            LEFT OUTER JOIN #__users u ON u.id = sz.user_id
            WHERE (u.params LIKE "%magyar%")
            )
            ');
        } else {
            echo echoHtmlDiv('createView hibas paraméter ' . $table, ERRORMSG);
            exit();
        }
        $db->query();
        if ($db->getErrorNum() != 0) {
            echo echoHtmlDiv($db->getErrorMsg(), ERRORMSG);
            exit();
        }
	}

	/**
   * szavazás eredményének megjelenitése
	* @param integer szavazás azonosító
   * @param JUser user 
   */ 	
   public function eredmeny($pollId, $user) {
			global $evConfig;
			$this->displayTestMsg($evConfig, $pollId);		
         $msg = '';
         $input = JFactory::getApplication()->input;
         $input->set('view','eredmeny');  
			$table = '#__'.$input->get('table','szavazatok',STRING);
         
			if (!teheti($pollId, $user, 'eredmeny', $msg)) {
    			echo echoHtmlDiv($msg, ERRORMSG);
                return;
			}
			$model = new SzavazokModel();
			$model->szavazatokTable = $table;
			$backUrl = JURI::root().'/leiras';
			// nézzük van-e cachelt report?
            $cache = $model->getFromCache($pollId);
        	$pollRecord = $model->getPollRecord($pollId);
			// ha nincs meg a cache rekord akkor hozzuk most létre, üres tartalommal
			if (!$cache) {
                $model->initCache($pollId);
				$cache = new stdClass();
				$cache->pollid = $pollId;
				$cache->report = "";
			}
			if ($cache->report == "") {
				// ha nincs; most  kell condorcet/Shulze feldolgozás feldolgozás
				if (!$this->checkView($table)) {
					$this->createView($table);				
				}
				$schulze = new MyCondorcet($pollId);
				$report = $schulze->report();
                $model->saveToCache($pollId, $schulze->vote_count, $report);
			} else {  
				// ha van akkor a cahcelt reportot jelenitjuük meg
				$report = $cache->report; 
			}
		   include dirname(__FILE__).'/views/eredmeny.php';
		   
		} // eredmeny function

		/**
		* sazavazás képernyő adat tárolása
		* JRequest: token, pollId, szavazat jelölt_id=pozicio, ......
		*/
		public function szavazatSave($pollId, $user) {
			global $evConfig;
			$this->cookieCheck();
			Jsession::checkToken() or die(INVALIDCSRTOKEN);
			$errorMsg = ERRORMSG;
			if ($evConfig->pollDefs[$pollId]->testMode) {
				$user->id = rand(100001,999999);
			}
			if ($user->id <= 0) {
    				echo echoHtmlDiv('Nincs bejelentkezve vagy lejárt a session',$errorMsg);
               return;
            }
			
            $input = JFactory::getApplication()->input;  
			$szavazat = $input->get('szavazat','',STRING);
			$msg = '';
			$msgClass = '';
			if ($pollId > 0) {
				if (teheti($pollId, $user, 'szavazas', $msg)) {
					$model = new SzavazokModel();
               $szavazoId = $model->save($pollId, $szavazat, $user);
					if ($szavazoId > 0) {
						$msg = 'Köszönjük szavazatát. Az ön szavazatának azonosítója az adatbázisban: <strong>'.$szavazoId.'</strong><br />'; 	
                  $msg .= '<small>Ennek az azonositónak a segitségével Ön ellenörizheti a letárolt szavazatát.';
                  $msg .=' Az eredmény menüpontban a "szavazatok" linkre kattintva láthatja a letárolt szavazatokat.</small>';
						$msgClass = INFOMSG;
					} else {
						$msg = 'Hiba a szavazat tárolása közben. A szavazat nem lett tárolva.'.$model->getErrorMsg();
						$msgClass = $errorMsg;
					}	
				} else {
					$msgClass = $errorMsg;
				}	
			} else {
				$msg = 'Nincs kiválasztva a szavazás';
				$msgClass = $errorMsg;
			}
			echo echoHtmlDiv($msg, $msgClass);
		}
	
		/**
		* leadott szavazatok listája
		* @param integer szavazas_id
		* @param JUser
		* @param string
		*/
		public function szavazatok($pollId, $user=null) {
			$model = new szavazokModel();
            $res = $model->getSzavazatok($pollId);
			if (count($res) > 0) {
				echo '<div class="szavazatok">
				<h2>'.$res[0]->szTitle.'</h2>
				<h3>Leadott szavazatok</h3>
				<ul>';
				$elozoSzavazo = 0;
				$elozoPozicio = 0;
				$pozicio = 0;
				foreach ($res as $res1) {
						if ($elozoSzavazo != $res1->szavazo_id) {
							echo '<li style="list-style:none" class="sparator">
							--------------'.$res1->szavazo_id.'------------
							</li>';
							$pozicio = 0;
							$elozoPozicio = 0;
						}
						if ($res1->pozicio <> $elozoPozicio) {
							$pozicio++;
						}
						echo '<li class="szavazat">'.$pozicio.'. '.$res1->altTitle.'</li>';
						$elozoSzavazo = $res1->szavazo_id;
						$elozoPozicio = $res1->pozicio;
				}
				echo '</ul>
				</div>
				';
			}
        }

		/**
		* leadott szavazatok listája CSV formátumban
		* @param integer szavazas_id
		* @param JUser
		* @param string
		*/
		public function szavazatokcsv($pollId, $user=null) {
		    if (!defined('UNITTEST')) {
		        define('UNITTEST',false);
		    }
		    if (!UNITTEST) {
                header('Content-type: text/csv');
                header('Pragma: no-cache');
                header('Expires:0');
                header('Content-Disposition: attachment: szavazatok.csv');
		    }
            $model = new szavazokModel();
				
            $res = $model->getSzavazatok($pollId);
			if (count($res) > 0) {
				echo 'szavazas_id;szavazo_id;pozicio;jelolt'."\n";
				foreach ($res as $res1) {
                    echo $res1->szavazas_id,';'.$res1->szavazo_id.';'.$res1->pozicio.';'.$res1->altTitle."\n";
				}
			}
			if (!UNITTEST) {
                jexit();
			}
        }
        
		/**
		* lista a javaslat kategoriában lévő jelölt javaslatokról (introtextel), sorrend: támogatottság + ABC
		* megjeleniti a "Támogatom" / támogatod "Mégsem támogatom" gombokat, az eddigi támogatások számát is.
		* "Új javaslatot küldök be" gomb is van
		*/	
      public function javaslatok() {
      	global $evConfig;
			$model = new JavaslatokModel();
			$user = JFactory::getUser();
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = BUDAPESTI;			
			}
      	    $javaslatok = $model->getJavaslatok($evConfig->pollDefs[$evConfig->pollId]->proposals, $user);
		    include dirname(__FILE__).'/views/javaslatok.php';
      }	

		/**
		* egy adott jelölt javaslat megjelenitése (fulltext-el))
		* megjeleniti a "Támogatom" / támogatod "Mégsem támogatom" gombokat, az eddigi támogatások számát is.
		*/
		public function javaslat() {
      	    global $evConfig;
			$model = new JavaslatokModel();
			$user = JFactory::getUser();
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = BUDAPESTI;			
			}
			$input = JFactory::getApplication()->input;
			$id = $input->get('id','');
			$javaslat = $model->getJavaslat($id,$user);
			if ($javaslat) {
 		         include dirname(__FILE__).'/views/javaslat.php';
			} else {
			    echo echoHtmlDiv($model->getErrorMsg(), ERRORMSG);
			    exit();
			}
		}
		
		/**
		* adott jelölt javaslat támogatásának tárolása
		*/
		public function tamogatom() {
      	global $evConfig;
      	$session = JFactory::getSession();
			$this->cookieCheck();
			$model = new JavaslatokModel();
			$user = JFactory::getUser();
			if (!$evConfig->pollDefs[$evConfig->pollId]->supportEnable) {
 					echo echoHtmlDiv('Jelenleg nem lehet támogatni a javaslatokat', ERRORMSG);
 					return;			
			}
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = BUDAPESTI;			
			}
			$input = JFactory::getApplication()->input;
			$id = $input->get('id','');
			if ($input->get($session->get(MYCSRTOKEN),'0') == 1) {
				if (strpos($user->params, $evConfig->pollDefs[$evConfig->pollId]->supportAssurance) > 0) {
					if (!$model->tamogatom($id, $user, true)) { 
						$this->setRedirect(JURI::base().JAVASLATOKURL);
					} else {
						$this->setMessage('A jelölt elérte a megkivánt támogatottságot. Át lett helyezve az elfogadott jelöltek közé.');
						$this->setRedirect(JURI::base().JAVASLATOKURL);
					} 
				}	else {
				    echo echoHtmlDiv(NOTASSURANCE, ERRORMSG);
					exit(); 
				}
				$this->redirect();
			} else {
			    echo echoHtmlDiv(INVALIDCSRTOKEN, ERRORMSG);
				exit();			
			}	
		}

		/**
		* adott jelölt javaslat támogatásának visszavonása
		*/
		public function nemtamogatom() {
      	global $evConfig;
      	$session = JFactory::getSession();
			$this->cookieCheck();
			$input = JFactory::getApplication()->input;
			if (!$evConfig->pollDefs[$evConfig->pollId]->supportEnable) {
			    echo echoHtmlDiv('Jelenleg nem lehet visszavonni a javaslat támogatást', ERRORMSG);
				return;			
			}
			if ($input->get($session->get(MYCSRTOKEN),'0') == 1) {
				$model = new JavaslatokModel();
				$user = JFactory::getUser();
				if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
					$user->id = 1;
					$user->params = BUDAPESTI;			
				}
				$id = $input->get('id','');
				if (strpos($user->params, $evConfig->pollDefs[$evConfig->pollId]->supportAssurance) > 0) {
					$model->tamogatom($id, $user, false);
					$this->setRedirect(JURI::base().JAVASLATOKURL);
				}	else {
				    echo echoHtmlDiv(NOTASSURANCE, ERRORMSG);
					exit(); 
				}
				$this->redirect();
			} else {
			    echo echoHtmlDiv(INVALIDCSRTOKEN, ERRORMSG);
				exit();			
			}	
		}

		/**
		* Új javaslat beküldési form
		*/
		public function javaslatform() {
      	global $evConfig;
			$this->cookieCheck();
			if (!$evConfig->pollDefs[$evConfig->pollId]->proposalEnable) {
			    echo echoHtmlDiv('Jelenleg nem lehet javaslatot beküldeni', ERRORMSG);
				return;			
			}
      	$user = JFactory::getUser();
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = BUDAPESTI;			
			}
			if (($user->id > 0) && 
			    (strpos($user->params, $evConfig->pollDefs[$evConfig->pollId]->supportAssurance) > 0)) {
			   	 include dirname(__FILE__).'/views/javaslatform.php';
		   }	
		}
		
		/** 
		* support funtion for javaslatSave
		* @return bool
		*/
		private function javaslatSave0() {
		        global $evConfig;  
		        $model = new JavaslatokModel();
		        $input = JFactory::getApplication()->input;
				$nev = $input->get('nev','',STRING);
				$program = $input->get('program','',STRING);
				$eletrajz = $input->get('eletrajz','',STRING);
				$tamogatok = $input->get('tamogatok','',STRING);
				$kontakt = $input->get('kontakt','',STRING);
				$kepUrl = $input->get('kepUrl','',STRING);
				$program = str_replace("\n",'<br />',$program);
				$eletrajz = str_replace("\n",'<br />',$eletrajz);
				$result = true;
				if ($nev == '') {
				    echo echoHtmlDiv('Jelölt nevet meg kell adni', ERRORMSG);
 					$result = false;			
				}
				if ($kepUrl == '') {
				    echo echoHtmlDiv('Kép URL -t meg kell adni', ERRORMSG);
 					$result = false;			
				}
				if ($program == '') {
				    echo echoHtmlDiv('Programot meg kell adni', ERRORMSG);
 					$result = false;			
				}
				if ($kontakt == '') {
				    echo echoHtmlDiv('kapcsolat információt meg kell adni', ERRORMSG);
 					$result = false;			
				}
				if ($result) {
				    $res = $model->JavaslatSave($evConfig, 
								$nev, $eletrajz, $program, $tamogatok, $kontakt, $kepUrl);
				    if ($res == '') {
				        $result = true;
				    } else {
				        $result = false;
				    }
				}
				return $result;				
		}		
		
		
		/**
		* Új javaslat tárolása (nem publikált státusszal)
		*/
		public function javaslatSave() {
      	global $evConfig;
      	$session = JFactory::getSession();
			$result = true;
			$this->cookieCheck();
			$input = JFactory::getApplication()->input;
			if ($input->get($session->get(MYCSRTOKEN)) != 1) {
			    echo echoHtmlDiv(INVALIDCSRTOKEN, ERRORMSG);
				$result = false;			
			}			
			
			if (!$evConfig->pollDefs[$evConfig->pollId]->proposalEnable) {
			    echo echoHtmlDiv('Jelenleg nem lehet új javaslatot beküldeni', ERRORMSG);
 				$result = false;			
			}
			$user = JFactory::getUser();
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = BUDAPESTI;			
			}
			if (($user->id > 0) && ($result)) {
			  $result = $this->javaslatSave0();				
			  if ($result) {
			      echo echoHtmlDiv('Javaslat tárolva. Ellenörzés után lesz publikálva', 'alert alert-success');
			  } else {
			      echo echoHtmlDiv('Hiba lépett fel a tárolás közben', ERRORMSG);
			  }
			} else {
			    echo echoHtmlDiv('Be kell jelentkezni', ERRORMSG);
			}
		}
		
		/**
		 * jelölt javaslati szakasz lezárásakor kell EGYSZER futtatni.
		 * a konfiguráció szerint a legtámogatottabb javaslatokat átteszi jelöltnek
		 */
		public function supportEnd() {
		    global $evConfig;
		    $pollId = $evConfig->pollId;
		    if ((!$evConfig->pollDefs[$pollId]->proposalEnable) &&
		        (!$evConfig->pollDefs[$pollId]->supportEnable)) {
		            $model = new JavaslatokModel();
		            if ($model->supportEnd($pollId, 
		                $evConfig->pollDefs[$pollId]->proposals,
		                $evConfig->pollDefs[$pollId]->requestedCandidateCount
		                )) {
		                echo echoHtmlDiv('jelölt javaslatok áttéve jelöltnek', INFOMSG);
		            } else {
		                echo echoHtmlDiv($model->getErrorMsg(), ERRORMSG);
		                
		            }
		     }
		}

	} // controller class
		
  // ================= main program ===========================
  $controller = new SzavazoController();
  $controller->$task ($pollId, $user);
?>
