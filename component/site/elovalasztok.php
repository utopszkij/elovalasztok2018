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
  include_once dirname(__FILE__).'/config.php';
  include_once dirname(__FILE__).'/accesscontrol.php';
  include_once dirname(__FILE__).'/funkciok.php';
  include_once dirname(__FILE__).'/condorcet.php';
  include_once dirname(__FILE__).'/models/szavazok.php';
  include_once dirname(__FILE__).'/models/javaslatok.php';
  
  $user = JFactory::getUser();
  $msg = '';
  $input = JFactory::getApplication()->input;  
  $pollId = $input->get('pollId',$evConfig->pollId);   // szavazás ID (category ID)
  $task = $input->get('task','szavazok');
  
  if ($pollId == 0) {
	  $pollId = $input->get('id',0);
  }

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
				echo '<div class="nincsJelolt infoMsg">Nincs jelölt!</div>';
		} else {
			  include dirname(__FILE__).'/views/szavazoform.php';
		}  
	}	

	/**
   * szavazó képernyő megjelenitése  - új szavazat beküldése
	* @param integer szavazás azonosító
   * @param JUser user 
   */ 	
    public function szavazok($pollId, $user) {
			global $evConfig;
			$session = JFactory::getSession();
			if ($session->get('cookie_enable','0') != 1) {
				echo echoHtmlDiv('Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!','errorMsg');
				exit();
			}
			$model = new SzavazokModel();
			$this->displayTestMsg($evConfig, $pollId);	
			if ($evConfig->pollDefs[$pollId]->testMode) {
				$this->displaySzavazoForm($pollId, $model);
				return;
			}		
			$msg = '';
			$infoMsg = 'infoMsg';
			if ($user->id == 0) {
					echo echoHtmlDiv('Szavazáshoz be kell jelentkezni!','errorMsg notLogin');
			} else if (!$evConfig->pollDefs[$pollId]->votingEnable) {
					echo echoHtmlDiv('Jelenleg nem lehet szavazni.',$infoMsg);
			} else  {
			  /* if (szavazottMar($pollId, $user)) {
					echo echoHtmlDiv('Ön már szavazott!',$infoMsg);
			  } else*/   if (teheti($pollId, $user, 'szavazas', $msg)) {
			  		$this->displaySzavazoForm($pollId, $model);
			  } else {
					echo echoHtmlDiv($msg,'errorMsg');
			  }
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
			$table = '#__'.$input->get('table','szavazatok','STRING');
         
			if (!teheti($pollId, $user, 'eredmeny', $msg)) {
    			echo '<div class="errorMsg">'.$msg.'</div>';
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
				$schulze = new MyCondorcet($pollId);
				
//TEST				$schulze->szavazatokTable = $table;
				
				
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
			$session = JFactory::getSession();
			if ($session->get('cookie_enable','0') != 1) {
				echo echoHtmlDiv('Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!','errorMsg');
				exit();
			}
			Jsession::checkToken() or die('invalid CSRF protect token');
			$errorMsg = 'errorMsg';
			if ($evConfig->pollDefs[$pollId]->testMode) {
				$user->id = rand(100001,999999);
			}
			if ($user->id <= 0) {
    				echo echoHtmlDiv('Nincs bejelentkezve vagy lejárt a session',$errorMsg);
               return;
            }
			
         $input = JFactory::getApplication()->input;  
			$szavazat = $input->get('szavazat','','STRING');
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
						$msgClass = 'infoMsg';
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
            header('Content-type: text/csv');
            header('Pragma: no-cache');
            header('Expires:0');
            header('Content-Disposition: attachment: szavazatok.csv');
				$model = new szavazokModel();
            $res = $model->getSzavazatok($pollId);
			if (count($res) > 0) {
				echo 'szavazas_id;szavazo_id;pozicio;jelolt'."\n";
				foreach ($res as $res1) {
                    echo $res1->szavazas_id,';'.$res1->szavazo_id.';'.$res1->pozicio.';'.$res1->altTitle."\n";
				}
			}
            jexit();
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
				$user->params = 'assurances:magyar,budapest';			
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
				$user->params = 'assurances:magyar,budapest';			
			}
			$input = JFactory::getApplication()->input;
			$id = $input->get('id','');
			$javaslat = $model->getJavaslat($id,$user);      	
		   include dirname(__FILE__).'/views/javaslat.php';
		}
		
		/**
		* adott jelölt javaslat támogatásának tárolása
		*/
		public function tamogatom() {
      	global $evConfig;
			$session = JFactory::getSession();
			if ($session->get('cookie_enable','0') != 1) {
				echo echoHtmlDiv('Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!','errorMsg');
				exit();
			}
			$model = new JavaslatokModel();
			$user = JFactory::getUser();
			if (!$evConfig->pollDefs[$evConfig->pollId]->supportEnable) {
 					echo '<div class="alert alert-dangeon">Jelenleg nem lehet támogatni</div>';
 					return;			
			}
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = 'assurances:magyar,budapest';			
			}
			$input = JFactory::getApplication()->input;
			$id = $input->get('id','');
			if ($input->get($session->get('myCsrToken'),'0') == 1) {
				if (strpos($user->params, $evConfig->pollDefs[$evConfig->pollId]->supportAssurance) > 0) {
					$model->tamogatom($id, $user, true);
					$this->setRedirect(JURI::base().'component/elovalasztok?task=javaslatok');
				}	else {
					echo '<p>Nincs megfelelő tanusítása</p>';
					exit(); 
				}
				$this->redirect();
			} else {
				echo '<p>Invalid CSR token</p>'; 
				exit();			
			}	
		}

		/**
		* adott jelölt javaslat támogatásának visszavonása
		*/
		public function nemtamogatom() {
      	global $evConfig;
			$session = JFactory::getSession();
			if ($session->get('cookie_enable','0') != 1) {
				echo echoHtmlDiv('Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!','errorMsg');
				exit();
			}
			$input = JFactory::getApplication()->input;
			if (!$evConfig->pollDefs[$evConfig->pollId]->supportEnable) {
 					echo '<div class="alert alert-dangeon">Jelenleg nem lehet támogatni</div>';
 					return;			
			}
			if ($input->get($session->get('myCsrToken'),'0') == 1) {
				$model = new JavaslatokModel();
				$user = JFactory::getUser();
				if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
					$user->id = 1;
					$user->params = 'assurances:magyar,budapest';			
				}
				$id = $input->get('id','');
				if (strpos($user->params, $evConfig->pollDefs[$evConfig->pollId]->supportAssurance) > 0) {
					$model->tamogatom($id, $user, false);
					$this->setRedirect(JURI::base().'component/elovalasztok?task=javaslatok');
				}	else {
					echo '<p>Nincs megfelelő tanusítása</p>';
					exit(); 
				}
				$this->redirect();
			} else {
				echo '<p>Invalid CSR token</p>'; 
				exit();			
			}	
		}

		/**
		* Új javaslat beküldési form
		*/
		public function javaslatform() {
      	global $evConfig;
			$session = JFactory::getSession();
			if ($session->get('cookie_enable','0') != 1) {
				echo echoHtmlDiv('Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!','errorMsg');
				exit();
			}
			if (!$evConfig->pollDefs[$evConfig->pollId]->proposalEnable) {
 					echo '<div class="alert alert-dangeon">Jelenleg nem lehet javasolni</div>';
 					return;			
			}
      	$user = JFactory::getUser();
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = 'assurances:magyar, budapest';			
			}
			if ($user->id > 0) {
				if (strpos($user->params, $evConfig->pollDefs[$evConfig->pollId]->supportAssurance) > 0) {
			   	include dirname(__FILE__).'/views/javaslatform.php';
			   }	
		   }	
		}
		
		/**
		* Új javaslat tárolása (nem publikált státusszal)
		*/
		public function javaslatSave() {
      	global $evConfig;
			$session = JFactory::getSession();
			if ($session->get('cookie_enable','0') != 1) {
				echo echoHtmlDiv('Ennek a programnak a használatához engedélyezni kell a csoki (cookie) tárolást!','errorMsg');
				exit();
			}
			$input = JFactory::getApplication()->input;
			
			if ($input->get($session->get('myCsrToken')) != 1) {
				echo 'invalid CSR token';
				exit();			
			}			
			
			if (!$evConfig->pollDefs[$evConfig->pollId]->proposalEnable) {
 					echo '<div class="alert alert-dangeon">Jelenleg nem lehet javasolni</div>';
 					return;			
			}
			$model = new JavaslatokModel();
			$user = JFactory::getUser();
			if ($evConfig->pollDefs[$evConfig->pollId]->testMode) {
				$user->id = 1;
				$user->params = 'assurances:magyar, budapest';			
			}
			if ($user->id > 0) {
				$nev = $input->get('nev','','STRING');
				$program = $input->get('program','','STRING');
				$eletrajz = $input->get('eletrajz','','STRING');
				$tamogatok = $input->get('tamogatok','','STRING');
				$kontakt = $input->get('kontakt','','STRING');
				$kepUrl = $input->get('kepUrl','','STRING');
				$program = str_replace("\n",'<br />',$program);
				$eletrajz = str_replace("\n",'<br />',$eletrajz);
				if ($nev == '') {
 					echo '<div class="alert alert-dangeon">Jelölt nevét meg kell adni</div>';
 					return;			
				}
				if ($kepUrl == '') {
 					echo '<div class="alert alert-dangeon">Kép URL -t meg kell adni</div>';
 					return;			
				}
				if ($program == '') {
 					echo '<div class="alert alert-dangeon">Programot meg kell adni</div>';
 					return;			
				}
				if ($kontakt == '') {
 					echo '<div class="alert alert-dangeon">Kapcsolat felvételi lehetőséget meg kell adni</div>';
 					return;			
				}
				
			   $result = $model->JavaslatSave($evConfig, 
					$nev, $eletrajz, $program, $tamogatok, $kontakt, $kepUrl);
					 
			  if ($result == '') {
 					echo '<div class="alert alert-success">Javaslat tárolva. A szerkesztők ellenörzése után kerül publikálásra.</div>';			
			  } else {
 					echo '<div class="alert alert-dangeon">'.$result.'</div>';			
			  }	
			} else {
				echo '<div class="alert alert-dangeon">Javaslat tételhez be kell jelentkezni</div>';			
			}
		}

	} // controller class
		
  // ================= main program ===========================
  $controller = new SzavazoController();
  $controller->$task ($pollId, $user);
?>
