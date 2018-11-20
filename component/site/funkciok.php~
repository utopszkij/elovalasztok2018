<?php
defined('_JEXEC') or die;

// elõválasztok rendszer globális funkciók, objektumok


/**
   * adott user, már szavazott?
	* ha nincs bejelentkezve akkor false az eredménye
	* @param integer $szavazas_id 
	* @param JUser $user
	* @param integer $fordulo
 */	
 function szavazottMar($szavazas_id, $user) {
	  $db = JFactory::getDBO();
	  $result = false;
	  if ($user->id > 0) {
			    $db->setQuery('select * from #__szavazatok where user_id='.$db->quote($user->id).' and szavazas_id = '.$db->quote($szavazas_id));
			    $res = $db->loadObjectList();
			    $result = (count($res) >= 1);
	  } else {
			$result = false;  
	  }
	  return $result;
 }

function szavazasraJogosult($user, $szavazas_id) {
	global $evConfig;
    if ($evConfig->pollDefs[$szavazas_id]->testUzemmod) {
        $result = true;
    } else if (($evConfig->pollDefs[$szavazas_id]->canAssurance != '') && (!$evConfig->pollDefs[$szavazas_id]->testUzemmod)) {
        $result = (strpos($user->params,$evConfig->pollDefs[$szavazas_id]->canAssurance) > 0);
    } else {
        $result = true;
    }
    return $result;
}

// hány szavazásra jogosult van az adott szavazásban?
function szavazokSzama($szavazas_id) {
    global $evConfig;
    $db = JFactory::getDBO();
    if (($evConfig->pollDefs[$szavazas_id]->canAssurance != '') && (!$evConfig->pollDefs[$szavazas_id]->testUzemmod)) {
        $db->setQuery('select count(id) cc
        from #__users
        where params like "%'.$evConfig->pollDefs[$szavazas_id]->canAssurance.'%" 
        ');
    } else {
        $db->setQuery('select count(id) cc
        from #__users
        ');   
    }
    $res = $db->loadObject();
    return $res->cc;
}

?>
