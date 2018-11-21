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
function teheti($pollId, $user, $akcio, &$msg) {
	global $evConfig;
	$result = false;
	$msg = '';
	
	if ($akcio == 'eredmeny') {
		   if ($evConfig->pollDefs[$pollId]->resultEnable) {
                $result = true;
                $msg = '';
           } else {
                $result = false;
                $msg = 'Jelenleg nem kérhető le az eredmény';
           }
	}
	
	if ($akcio == 'szavazas') {
          if ($evConfig->pollDefs[$pollId]->testMode) {
              $msg = '';  
              $result = true;  
          } else if ($user->id <= 0) {
			  $result = false;
			  $msg = 'Szavazáshoz be kell jelentkezni!';
	 	  } else if (szavazottMar($pollId, $user)) {  
			  $result = false;
			  $msg = 'Ön már szavazott';
		  }  else {
			  if (szavazasraJogosult($user, $pollId)) {
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
