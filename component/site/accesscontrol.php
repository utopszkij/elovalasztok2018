<?php
/**
  * elovalasztok acces control          include file
  *
  * Licensz: GNU/GPL
  * Szerző: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  */
  defined('_JEXEC') or die;
  global $evConfig;
  
/**
* engedélyezett/nem egedélyezett az akció?
* @param integer oevk 
* @param Juser bejelentkezett user
* @param string $akcio 'jeloltAdd','jeloltEdit','jeloltDelete','szavazas','szavazatEdit','szavazatDelete','eredmeny'
* @param string $msg output parameter: tiltás oka pl: 'config'
* @return boolean
*/
function teheti($szavazas_id, $user, $akcio, &$msg) {
	global $evConfig;
	if ($evConfig->testUzemmod) {
			return true;
	}
	$result = false;
	$msg = '';
	$fordulo = $evConfig->fordulo;
	$db = JFactory::getDBO();
	$db->setQuery('select * from #__categories where id='.$db->quote($szavazas_id).' and published = 1');
	$szavazas = $db->loadObject();
	
	if ($akcio == 'eredmeny') {
		   $result = true;
	}
	
	// lezárt szavazás kezelése
	if ($akcio == 'szavazas') {
	   if (strpos($szavazas->title,'(lezárt)')) {	
		   $result = false;
		   $msg = 'Lezárt szavazás';	
		   return $result;
	   }	   
	}
	
	if ($user->id <= 0) {
	   $msg = 'Jelentkezzen be!';
	   return false;
	}	

	if ($akcio == 'szavazas') {
	 	  if (szavazottMar($szavazas_id, $user, $fordulo)) {  
			  $result = false;
			  $msg = 'Ön már szavazott';
		  }  else {
			  if (szavazasraJogosult($user, $szavazas_id, '')) {
				$result = true;
				$msg = '';
			  } else {
				$result = false;
				$msg = 'Ön ebben a szavazásban nem szavazhat';
			  }	
		  } 
	}   
	return $result;
}
 
?>
