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
  
  $user = JFactory::getUser();
  $msg = '';
  $input = JFactory::getApplication()->input;  
  $pollId = $input->get('pollId',$evConfig->pollId);   // szavazás ID (category ID)
  $task = $input->get('task','szavazok');
  
  if ($pollId == 0) {
	  $pollId = $input->get('id',0);
  }

  // ================ controller ==============================
  class szavazoController extends JcontrollerLegacy {
	  
	/**
    * szavazó képernyő megjelenitése  - új szavazat beküldése
	* @param integer szavazás azonosító
    * @param JUser user 
    */ 	
    public function szavazok($pollId, $user) {
			global $evConfig;
			$model = new szavazokModel();
		
			if ($evConfig->pollDefs[$pollId]->testMode) {
				echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
		        $item = $model->getItem($pollId);	
       		    include dirname(__FILE__).'/views/szavazoform.php';
				return;
			}

			$msg = '';
			if ($user->id == 0) {
					echo '<div class="notLogin">Szavazáshoz be kell jelentkezni!</div>';
			} else if (!$evConfig->pollDefs[$pollId]->votingEnable) {
					echo '<div class="notLogin">Jelenleg nem lehet szavazni.</div>';
			} else  {
			  if (szavazottMar($pollId, $user)) {
					echo '<div class="marSzavazaott infoMsg">Ön már szavazott!</div>';
			  } else {
				  if (teheti($pollId, $user, 'szavazas', $msg)) {
				   $item = $model->getItem($pollId);	
					if (count($item->pollOptions) <= 0) {
							echo '<div class="nincsJelolt infoMsg">Nincs jelölt!</div>';
					} else {
						  include dirname(__FILE__).'/views/szavazoform.php';
					}  
				  } else {
					echo '<div class="nemSzavazhat infoMsg">'.$msg.'</div>';
				  }
			  } // szavazott már?	 
			}
	}
	
	
	/**
    * szavazás eredményének megjelenitése
	* @param integer szavazás azonosító
    * @param JUser user 
    */ 	
    public function eredmeny($pollId, $user) {
			global $evConfig;
			if ($evConfig->pollDefs[$pollId]->testMode) {
				echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
			}
            $msg = '';
			if (!teheti($pollId, $user, 'eredmeny', $msg)) {
    			echo '<div class="errorMsg">'.$msg.'</div>';
                return;
			}
			$model = new szavazokModel();
			$backUrl = JURI::root().'/leiras';
            $pollRecord = $model->getPollRecord($pollId);
			// nézzük van-e cachelt report?
            $cache = $model->getFromCache($pollId);

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
			Jsession::checkToken() or die('invalid CSRF protect token');
			if ($evConfig->pollDefs[$pollId]->testMode) {
				$user->id = rand(100001,999999);
			}
			if ($user->id <= 0) {
    			echo '<div class="errorMsg">Nincs bejelentkezve vagy lejárt a session</div>';
                return;
            }
			
            $input = JFactory::getApplication()->input;  
			$szavazat = $input->get('szavazat','','STRING');
			$msg = '';
			$msgClass = '';
			if ($pollId > 0) {
				if (teheti($pollId, $user, 'szavazas', $msg)) {
					$model = new szavazokModel();
                    $szavazoId = $model->save($pollId, $szavazat, $user);
					if ($szavazoId > 0) {
						$msg = 'Köszönjük szavazatát. Az ön szavazatának azonosítója az adatbázisban: <strong>'.$szavazoId.'</strong><br />'; 	
                        $msg .= '<small>Ennek az azonositónak a segitségével Ön ellenörizheti a letárolt szavazatát.';
                        $msg .=' Az eredmény menüpontban a "szavazatok" linkre kattintva láthatja a letárolt szavazatokat.</small>';
						$msgClass = 'infoMsg';
					} else {
						$msg = 'Hiba a szavazat tárolása közben. A szavazat nem lett tárolva '.$model->getErrorMsg();
						$msgClass = 'errorMsg';
					}	
				} else {
					$msgClass = 'errorMsg';
				}	
			} else {
				$msg = 'Nincs kiválasztva a szavazás';
				$msgClass = 'errorMsg';
			}
			echo '<div class="'.$msgClass.'">'.$msg.'</div>';
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

	} // controller class
		
  // ================= main program ===========================
  $controller = new SzavazoController();
  $controller->$task ($pollId, $user);
?>
