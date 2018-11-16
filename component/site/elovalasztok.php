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
  $filter = $input->get('filter','','STRING');
  $task = $input->get('task','szavazok');
  $secret = $input->get('secret');
  $id = $input->get('id',0);
  $szavazatTable = $input->get('szavazattable','#__szavazatok','STRING');
  
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
    * @param string filter
    */ 	
    public function szavazok($oevk, $user, $filter, $szavazatTable='') {
			global $evConfig;
			$model = new szavazokModel();
		
			if ($evConfig->testUzemmod) {
				echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
		      $item = $model->getItem($oevk);	
       		    include_once dirname(__FILE__).'/views/szavazoform.php';
				$this->mysqlUserToken($user);
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
						 $this->mysqlUserToken($user);
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
    * @param string filter
    */ 	
    public function eredmeny($oevk, $user, $filter, $szavazatTable = '#__szavazatok') {
			global $evConfig;

			/* oevk check, repair
			if (!isOevkSzavazas($oevk)) {
				$oevk = oevkFromJelolt($oevk);
			}
			if (!isOevkSzavazas($oevk)) {
				$oevk = oevkFromUser($user);
			}
			*/
		
			if ($evConfig->testUzemmod) {
				echo '<div class="testInfo"><strong>Teszt üzemód. Bárki szavazhat, többször is lehet szavazni.</strong></div>';			
			}
		
			$db = JFactory::getDBO();
			$this->mysqlUserToken($user);
			$model = new szavazokModel();
			include_once JPATH_ROOT.'/components/com_elovalasztok/condorcet.php';
			$backUrl = JURI::root().'/leiras';
			$organization = '';
			$db->setQuery('select * from #__categories where id='.$db->quote($oevk));
			$poll = $db->loadObject();
			echo '<h2>'.$poll->title.'</h2>
			<div class="pollLeiras">'.$poll->description.'</div>
			';
			$pollid = $oevk;
		
			// nézzük van-e cachelt report?
			$db->setQuery('select * from 
						 #__eredmeny 
						 where pollid='.$db->quote($pollid).' and 
						 filter='.$db->quote($szavazatTable).' and
						 fordulo = 0');
			$cache = $db->loadObject();
//TEST $cache = false;			
		
			// ha nincs meg a cache rekord akkor hozzuk most létre, üres tartalommal
			if ($cache == false) {
				$db->setQuery('INSERT INTO #__eredmeny
				(pollid, report,filter,fordulo ) 
				value 
				('.$db->quote($pollid).',"","'.$szavazatTable.'",'.$db->quote($evConfig->fordulo).')');
				$db->query();
				$cache = new stdClass();
				$cache->pollid = $pollid;
				$cache->filter = $filter;
				$cache->fordulo = 0;
				$cache->report = "";
			}
		
			if ($cache->report == "") {
				// ha nincs; most  kell condorcet/Shulze feldolgozás feldolgozás
				$schulze = new Condorcet($db,$organization,$pollid,$filter,$evConfig->fordulo);
				$schulze->szavazatTable = $szavazatTable;
				$report = $schulze->report();
				$db->setQuery('update #__eredmeny 
				set report='.$db->quote($report).',
					c1 = '.$schulze->c1.',
					c2 = '.$schulze->c2.',
					c3 = '.$schulze->c3.',
					c4 = '.$schulze->c4.',
					c5 = '.$schulze->c5.',
					c6 = '.$schulze->c6.',
					c7 = '.$schulze->c7.',
					c8 = '.$schulze->c8.',
					c9 = '.$schulze->c9.',
					c10 = '.$schulze->c10.',
					vote_count = '.$schulze->vote_count.'
				where pollid="'.$pollid.'" and filter='.$db->quote($szavazatTable).' and fordulo='.$db->quote($evConfig->fordulo));
				$db->query();
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
		public function szavazatSave($oevk, $user, $filter,$szavazatTable='') {
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
					$secret = rand(100000,999999);
					$this->mysqlUserToken($user);
					if ($model->save($oevk, $szavazat, $user, $evConfig->fordulo, $secret)) {
						$msg = 'Köszönjük szavazatát.'; 	
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
		* user aktivitás jelző token tárolása az adatbázisba és az elavultak (1 óránál régebbiek) törlése
		*/
		protected function mysqlUserToken($user) {
			$db = JFactory::getDBO();
			$db->setQuery('CREATE TABLE IF NOT EXISTS #__usertoken (
				 user_id varchar(128),
				 idopont datetime,
				 PRIMARY KEY (`user_id`)
			)');
			$db->query();
			$db->setQuery('delete from #__usertoken where idopont < "'.date('Y-m-d H:i:s', time() - 60*60).'"');
			$db->query();
			$db->setQuery('select * from #__usertoken where user_id='.$db->quote(sha1($user->id)));
			$res = $db->loadObject();
			if ($res)
				$db->setQuery('update #__usertoken set idopont="'.date('Y-m-d H:i:s').'" where user_id='.$db->quote(sha1($user->id)));
			else
				$db->setQuery('insert into #__usertoken values ('.$db->quote(sha1($user->id)).',"'.date('Y-m-d H:i:s').'")');
			$db->query();	
		}
  

		/**
		* leadott szavazatok listája
		* @param integer szavazas_id
		* @param JUser
		* @param string
		*/
		public function szavazatok($oevk, $user=null, $filter='', $szavazatTable = '#__szavazatok') {
			$db = JFactory::getDBO();
			$db->setQuery('select sz.szavazas_id, sz.szavazo_id, sz.pozicio, c2.title altTitle,
			c1.title szTitle
			from '.$szavazatTable.' sz
			left outer join #__content c2 on c2.id = sz.alternativa_id
			left outer join #__categories c1 on c1.id = sz.szavazas_id
			where c2.state = 1 and 
			sz.szavazas_id = '.$oevk.'
			order by 1,2,3
			');
			$res = $db->loadObjectList();
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
							--------------user'.$res1->szavazo_id.'------------
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
		public function szavazatokcsv($oevk, $user=null, $filter='', $szavazatTable = '#__szavazatok') {
			$db = JFactory::getDBO();
			$db->setQuery('select sz.szavazas_id, sz.szavazo_id, sz.pozicio, c2.title altTitle,
			c1.title szTitle
			from '.$szavazatTable.' sz
			left outer join #__content c2 on c2.id = sz.alternativa_id
			left outer join #__categories c1 on c1.id = sz.szavazas_id
			where c2.state = 1 and 
			sz.szavazas_id = '.$oevk.'
			order by 1,2,3
			');
			$res = $db->loadObjectList();
			if (count($res) > 0) {
				echo '<pre><code>'."\n".'szavazas_id, szavazo_id, pozicio, altTitle'."\n";
				foreach ($res as $res1) {
                    echo $res1->szavazas_id,';'.$res1->szavazo_id.','.$res1->pozicio.','.$res1->altTitle."\n";
				}
                echo '</code></pre>'."\n";
			}
        }

		public function statisztika($oevk, $user, $filter, $szavazatTable) {
				$db = JFactory::getDBO();

				$db->setQuery('SELECT COUNT(*) AS cc
				FROM #__users where name not like "testuser%"');
				$totalUsers = $db->loadObject();

				$db->setQuery('SELECT COUNT(*) AS cc
				FROM #__users
				WHERE params LIKE "%magyar%"');
				$hitelesUsers = $db->loadObject();
				$db->setQuery('SELECT oevk.title, oevk.id AS oevk_id,
								(COUNT(DISTINCT jelolt.id) - 1) AS jeloltek, 
								COUNT(DISTINCT szavazat.szavazo_id) AS szavazatok 
						FROM #__categories AS oevk
						INNER JOIN #__content AS jelolt ON jelolt.catid = oevk.id
						LEFT OUTER JOIN #__szavazatok AS szavazat ON szavazat.alternativa_id = jelolt.id
						WHERE (oevk.parent_id = 8  OR oevk.id = 155 OR oevk.id = 156) AND oevk.published = 1 AND 
								jelolt.state = 1
						GROUP BY oevk.id
						ORDER BY oevk.title;
				');
				$res = $db->loadObjectList();
				?>
				<center>
				<h2>Statisztika</h2>
				<p>Összes regisztrált felhasználó:<?php echo $totalUsers->cc;  ?></p>
				<p>ADA hiteles magyar felhasználó:<?php echo $hitelesUsers->cc;  ?></p>
				<table style="width:80%">
					<thead style="background-color:black; color:white;">
						<tr>
						<th>&nbsp;Szavazás</th>
						<th>&nbsp;Jelöltek száma</th>
						<th>&nbsp;Szavazatok száma</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($res as $res1) : ?>
						<tr>
							<td>
								<a href="./component/content/category?id=<?php echo $res1->oevk_id; ?>">
								&nbsp;<?php echo $res1->title; ?>
								</a>
							</td>
							<td align="right"><?php echo $res1->jeloltek; ?>&nbsp;</td>
							<td align="right"><?php echo $res1->szavazatok; ?>&nbsp;</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table> 
				</center>
				<?php
		}

	} // controller class
		
  // ================= main program ===========================
  $controller = new SzavazoController();
  $controller->$task ($oevk, $user, $filter, $szavazatTable);
?>
