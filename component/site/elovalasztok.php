<?php
/**
  * szavazok component
  *   taskok: szavazok, szavazatedit, szavazatDelete, eredmeny, szavazatSave
  * Licensz: GNU/GPL
  * Szerző: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  * JRequest: oevk, task
  */
  defined('_JEXEC') or die;
  global $evConfig;
  include_once dirname(__FILE__).'/config.php';
  include_once dirname(__FILE__).'/accesscontrol.php';
  include_once dirname(__FILE__).'/models/szavazok.php';
  include_once dirname(__FILE__).'/funkciok.php';
  
  $user = JFactory::getUser();
  $msg = '';
  $input = JFactory::getApplication()->input;  
  $oevk = $input->get('oevk',$evConfig->pollId);
  $task = $input->get('task','szavazok');
  $secret = $input->get('secret');
  $id = $input->get('id',0);
  
  if ($oevk == 0) {
	  $oevk = $id;
  }
  
  function base64url_encode2($data) { 
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
  } 
 

  // ================ controller ==============================
  class szavazoController extends JcontrollerLegacy {
	  
	/**
    * szavazó képernyő megjelenitése  - új szavazat beküldése
	* @param integer szavazás azonosító
    * @param JUser user 
    */ 	
    public function szavazok($oevk, $user) {
			global $evConfig;
			$model = new szavazokModel();
		
			if ($evConfig->testUzemmod) {
				echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
		      $item = $model->getItem($oevk);	
       		    include_once dirname(__FILE__).'/views/szavazoform.php';
				return;
			}

			$msg = '';
			if ($user->id == 0) {
					echo '<div class="notLogin">Szavazáshoz be kell jelentkezni!</div>';
			} else if (!$evConfig->szavazas) {
					echo '<div class="notLogin">Jelenleg nem lehet szavazni.</div>';
			} else  {
			 if (szavazottMar($oevk, $user)) {
					echo '<div class="marSzavazaott infoMsg">Ön már szavazott!</div>';
				 } else {
				  if (teheti($oevk, $user, 'szavazas', $msg)) {
				    $item = $model->getItem($oevk);	
						if (count($item->alternativak) <= 0) {
							echo '<div class="marSzavazaott infoMsg">Ön már szavazott!</div>';
						} else {
						 include_once dirname(__FILE__).'/views/szavazoform.php';
						}  
				  } else {
					echo '<div class="nemSzavazhat infoMsg">Ön nem szavazhat!</div>';
				  }
				} // szavazott már?	 
			}
	}
	
	
	/**
    * szavazás eredményének megjelenitése
	* @param integer szavazás azonosító
    * @param JUser user 
    */ 	
    public function eredmeny($oevk, $user) {
			global $evConfig;
			if ($evConfig->testUzemmod) {
				echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
			}
		
			$model = new szavazokModel();
			include_once JPATH_ROOT.'/components/com_elovalasztok/condorcet.php';
			$backUrl = JURI::root().'/leiras';
            $pollRecord = $model->getPollRecord($oevk);
			// nézzük van-e cachelt report?
            $cache = $model->getFromCache($oevk);
			// ha nincs meg a cache rekord akkor hozzuk most létre, üres tartalommal
			if ($cache == false) {
                $model->initCache($oevk);
				$cache = new stdClass();
				$cache->pollid = $oevk;
				$cache->report = "";
			}
			if ($cache->report == "") {
				// ha nincs; most  kell condorcet/Shulze feldolgozás feldolgozás
				$schulze = new Condorcet($oevk);
				$report = $schulze->report();
                $model->saveToCache($oevk, $schulze->vote_count, $report);
			} else {  
				// ha van akkor a cahcelt reportot jelenitjuük meg
				$report = $cache->report; 
			}
			include JPATH_ROOT.'/components/com_elovalasztok/views/eredmeny.php';
		} // eredmeny function

		/**
		* sazavazás képernyő adat tárolása
		* JRequest: token, oevk, szavazat jelölt_id=pozicio, ......
		*/
		public function szavazatSave($oevk, $user) {
			global $evConfig;
			Jsession::checkToken() or die('invalid CSRF protect token');
			if ($evConfig->testUzemmod) {
				$user->id = rand(100001,999999);
			}
			if ($user->id <= 0) die('nincs bejelentkezve, vagy lejárt a session');
			$input = JFactory::getApplication()->input;  
			$szavazat = $input->get('szavazat','','STRING');
			$msg = '';
			$msgClass = '';
			if ($oevk > 0) {
				$akcio = 'szavazas'; 
				if (teheti($oevk, $user, $akcio, $msg)) {
					$model = new szavazokModel();
                    $szavazoId = $model->save($oevk, $szavazat, $user);
					if ($szavazoId > 0) {
						$msg = 'Köszönjük szavazatát. Az ön szavazatának azonosítója az adatbázisban: <strong>'.$szavazoId.'</strong><br />'; 	
                        $msg .= '<small>Ennek az azonositónak a segitségével Ön ellenörizheti a letárolt szavazatát.';
                        $msg .=' Az eredmény menüpontban a "szavazatok" linkre kattintva láthatja a letárolt szavazatokat.</small>';
						$msgClass = 'infoMsg';
					} else {
						$msg = 'Hiba a szavazat tárolása közben. A szavazat nem lett tárolva';
						$msgClass = 'errorMsg';
					}	
				} else {
					$msg = 'Ön nem szavazhat!';
					$msgClass = 'errorMsg';
				}	
			} else {
				$msg = 'Nincs kiválasztva a választó kerület';
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
		public function szavazatok($oevk, $user=null) {
			$model = new szavazokModel();
            $res = $model->getSzavazatok($oevk);
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
		public function szavazatokcsv($oevk, $user=null) {
			$model = new szavazokModel();
            $res = $model->getSzavazatok($oevk);
			if (count($res) > 0) {
				echo '<pre><code>'."\n".'szavazas_id, szavazo_id, pozicio, altTitle'."\n";
				foreach ($res as $res1) {
                    echo $res1->szavazas_id,';'.$res1->szavazo_id.','.$res1->pozicio.','.$res1->altTitle."\n";
				}
                echo '</code></pre>'."\n";
			}
        }

	} // controller class
		
  // ================= main program ===========================
  $controller = new SzavazoController();
  $controller->$task ($oevk, $user);
?>
