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
    if (($evConfig->canAssurance != '') && (!$evConfig->testUzemmod)) {
       $db = JFactory::getDBO();
       $db->setQuery('select count(id) cc
       from #__users 
       where id='.$db->quote($user->id).' and params like "%'.$evConfig->canAssurance.'%"' );   
       return ($db->loadObject()->cc > 0);
    } else {
       return true;
    }
}

// hány szavazásra jogosult van az adott szavazásban?
function szavazokSzama($szavazas_id) {
    global $evConfig;
    $db = JFactory::getDBO();
    if (($evConfig->canAssurance != '') && (!$evConfig->testUzemmod)) {
        $db->setQuery('select count(id) cc
        from #__users
        where params like "%'.$evConfig->canAssurance.'%" 
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
