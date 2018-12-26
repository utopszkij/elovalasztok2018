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
  
  
function tehetiEredmeny($evConfig, $pollId, &$msg) {
   if ($evConfig->pollDefs[$pollId]->resultEnable) {
        $result = true;
        $msg = '';
   } else {
        $result = false;
        $msg = 'Jelenleg nem kérhető le az eredmény '.JSON_encode($evConfig);
   }
	return $result;
}  

function teheteiSzavazas($evConfig, $pollId, $user, &$msg) {
	$result = true;
	if (!$evConfig->pollDefs[$evConfig->pollId]->votingEnable) {
		$msg = 'Most nem lehet szavazni.';
		$result = false;
	} else if (!szavazasraJogosult($user, $pollId)) {
		$msg = 'Ön ebben a szavazásban nem szavazhat';
		$result = false;
	}	
   return $result;
}
  
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
		$result = tehetiEredmeny($evConfig, $pollId, $msg);
	}
	if ($akcio == 'szavazas') {
		$result = teheteiSzavazas($evConfig, $pollId, $user, $msg);
	}   
	return $result;
}
 
?>
