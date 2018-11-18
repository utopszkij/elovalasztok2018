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
	$result = false;
	$msg = '';
	$db = JFactory::getDBO();
	$db->setQuery('select * from #__categories where id='.$db->quote($szavazas_id).' and published = 1');
	$szavazas = $db->loadObject();
	
	if ($akcio == 'eredmeny') {
		   if ($evConfig->eredmeny) {
                $result = true;
                $msg = '';
           } else {
                $result = false;
                $msg = 'Jelenleg nem kérhető le az eredmény';
           }
	}
	
	if ($akcio == 'szavazas') {
          if ($evConfig->testUzemmod) {
              $msg = '';  
              $result = true;  
          } else if ($user->id <= 0) {
			  $result = false;
			  $msg = 'Szavazáshoz be kell jelentkezni!';
	 	  } else if (szavazottMar($szavazas_id, $user)) {  
			  $result = false;
			  $msg = 'Ön már szavazott';
		  }  else {
			  if (szavazasraJogosult($user, $szavazas_id)) {
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
